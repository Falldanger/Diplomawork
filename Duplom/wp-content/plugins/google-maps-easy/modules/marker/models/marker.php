<?php
class markerModelGmp extends modelGmp {
    public static $tableObj;
    function __construct() {
		$this->_setTbl('marker');
        if(empty(self::$tableObj)){
            self::$tableObj = frameGmp::_()->getTable('marker');
        }
    }
	public function save($marker = array(), &$update = false) {
		$id = isset($marker['id']) ? (int) $marker['id'] : 0;
		$marker['title'] = isset($marker['title']) ? trim($marker['title']) : '';
		$marker['coord_x'] = isset($marker['coord_x']) ? (float)$marker['coord_x'] : 0;
		$marker['coord_y'] = isset($marker['coord_y']) ? (float)$marker['coord_y'] : 0;
		if(!empty($marker['period_date_from']) && strtotime($marker['period_date_from'])) {
			$marker['period_from'] = date('Y-m-d', strtotime($marker['period_date_from']));
		}
		if(!empty($marker['period_date_to']) && strtotime($marker['period_date_to'])) {
			$marker['period_to'] = date('Y-m-d', strtotime($marker['period_date_to']));
		}
		$update = (bool) $id;
		if(!empty($marker['title'])) {
			$marker = apply_filters('gmp_before_marker_save', $marker);
			if(isset($marker['description'])){
				//Replace site url in markers descriptions backend. Fixed site migration issue.
				$marker['description'] = str_replace(GMP_SITE_URL, 'GMP_SITE_URL', $marker['description']);
			}
			if(!isset($marker['marker_group_id'])) {
				$marker['marker_group_id'] = 0;
			}
			if(!isset($marker['icon']) || !frameGmp::_()->getModule('icons')->getModel()->iconExists($marker['icon'])) {
				$marker['icon'] = 1;
			}
			$marker['map_id'] = isset($marker['map_id']) ? (int) $marker['map_id'] : 0;
			if(!$update) {
				$marker['create_date'] = date('Y-m-d H:i:s');
				if($marker['map_id']) {
					$maxSortOrder = (int) dbGmp::get('SELECT MAX(sort_order) FROM @__markers WHERE map_id = "'. $marker['map_id']. '"', 'one');
					$marker['sort_order'] = ++$maxSortOrder;
				}
			}

			//save first groups value in markers table to better compatibility
			$markerGroupIds = $marker['marker_group_id'];
			$marker['marker_group_id'] = $first_value = reset($markerGroupIds);;

			$marker['params'] = isset($marker['params']) ? utilsGmp::serialize($marker['params']) : '';
			if($update) {
				dispatcherGmp::doAction('beforeMarkerUpdate', $id, $marker);
				$dbRes = frameGmp::_()->getTable('marker')->update($marker, array('id' => $id));

				frameGmp::_()->getTable('marker_groups_relation')->delete('marker_id = ' . $marker['id']);
				foreach ($markerGroupIds as $markerId) {
					frameGmp::_()->getTable('marker_groups_relation')->insert(array('marker_id'=>$marker['id'], 'groups_id'=>$markerId));
				}
				dispatcherGmp::doAction('afterMarkerUpdate', $id, $marker);
			} else {
				dispatcherGmp::doAction('beforeMarkerInsert', $marker);
				$dbRes = frameGmp::_()->getTable('marker')->insert($marker);
				if($dbRes){
					frameGmp::_()->getTable('marker_groups_relation')->delete('marker_id = ' . $dbRes);
					foreach ($markerGroupIds as $markerId) {
						frameGmp::_()->getTable('marker_groups_relation')->insert(array('marker_id' => $dbRes, 'groups_id' => $markerId));
					}
				}
				dispatcherGmp::doAction('afterMarkerInsert', $dbRes, $marker);
			}
			if($dbRes) {
				if(!$update) {
					$id = $dbRes;
				}
				do_action('gmp_save_lang_data', array(
					'type' => 'markers',
					'marker_id' => $id,
					'map' => frameGmp::_()->getModule('gmap')->getModel()->getMapById($marker['map_id']),
				));
				return $id;
			} else {
				$this->pushError(frameGmp::_()->getTable('marker')->getErrors());
			}
		} else {
			$this->pushError(__('Please enter marker name', GMP_LANG_CODE), 'marker_opts[title]');
		}
		return false;
	}
	public function existsId($id){
		if($id){
			$marker = frameGmp::_()->getTable('marker')->get('*', array('id' => $id), '', 'row');
			if(!empty($marker)){
				return true;
			}
		}
		return false;
	}
	public function getById($id) {
		return $this->_afterGet(frameGmp::_()->getTable('marker')->get('*', array('id' => $id), '', 'row'));
	}
	public function getMarkerByTitle($title) {
		return $this->_afterGet(frameGmp::_()->getTable('marker')->get('*', array('title' => $title), '', 'row'));
	}
	public function _afterGet($marker, $widthMapData = false, $withoutIcons = false) {
		if(!empty($marker)) {
			if(!$withoutIcons) {
				$marker['icon_data'] = frameGmp::_()->getModule('icons')->getModel()->getIconFromId($marker['icon']);
			}
			$marker['params'] = utilsGmp::unserialize($marker['params']);
			/*$marker['position'] = array(
				'coord_x' => $marker['coord_x'],
				'coord_y' => $marker['coord_y'],
			);*/
			if(isset($marker['params']['marker_title_link']) 
				&& !empty($marker['params']['marker_title_link']) 
				&& strpos($marker['params']['marker_title_link'], 'http') !== 0
			) {
				$marker['params']['marker_title_link'] = 'http://'. $marker['params']['marker_title_link'];
			}
			if(!isset($marker['params']['title_is_link']))
				$marker['params']['title_is_link'] = false;

			$siteUrl = uriGmp::isHttps() ? uriGmp::makeHttps(GMP_SITE_URL) : GMP_SITE_URL;
			// Go to absolute path as "../wp-content/" will not work on frontend
			$marker['description'] = str_replace('../wp-content/', $siteUrl. 'wp-content/', $marker['description']);
			//Replace site url in markers descriptions frontend.
			$marker['description'] = str_replace('GMP_SITE_URL', $siteUrl, $marker['description']);

			//if(uriGmp::isHttps()) {
			//	$marker['description'] = uriGmp::makeHttps($marker['description']);
			//}
			if($widthMapData && !empty($marker['map_id']))
				$marker['map'] = frameGmp::_()->getModule('gmap')->getModel()->getMapById($marker['map_id'], false);

			if($widthMapData && !empty($marker['map_id']))
				$marker['map'] = frameGmp::_()->getModule('gmap')->getModel()->getMapById($marker['map_id'], false);

			if(!empty($marker['id'])){
				$marker['marker_group_ids'] = frameGmp::_()->getModule('marker')->getModel('marker_groups_relation')->getRelationsByMarkerId($marker['id']);
			}
		}
		return $marker;
	}

	/*public function saveMarkers($markerArr, $mapId) {
        foreach($markerArr as $marker) {
			 $marker['map_id'] = $mapId;
             $this->saveMarker($marker);
        }
        return !$this->haveErrors();
    }
	public function saveMarker($marker) {
		if(!isset($marker['marker_group_id'])) {
			$marker['marker_group_id'] = 1;
		}
		if(!isset($marker['icon'])) {
			$marker['icon'] = 1;
		} elseif(!frameGmp::_()->getModule('icons')->getModel()->iconExists($marker['icon'])) {
			// Why here is echo??? I don't know.........
			//echo $marker['icon'].".."; 
			$marker['icon'] = 1;
		}
		unset($marker['id']);
		$marker['create_date'] = date('Y-m-d H:i:s');
		$marker['params'] = utilsGmp::serialize(array('titleLink' => $marker['titleLink']));
		unset($marker['titleLink']);
		if(!frameGmp::_()->getTable('marker')->insert($marker)) {
			$this->pushError(frameGmp::_()->getTable('marker')->getErrors());
		}
	}*/
    /*public function updateMapMarkers($params, $mapId = null) {
        foreach($params as $id => $data) {
			//self::$tableObj->delete($id);
			$newId = $id;
            $exists = self::$tableObj->exists($id);
            unset($data['id']);
            if($mapId) {
				$data['map_id'] = $mapId;
            }
            $data['marker_group_id'] = $data['groupId'];
			$data['params'] = utilsGmp::serialize(array('titleLink' => $data['titleLink']));
			unset($data['titleLink']);
            if($exists) {
                self::$tableObj->update($data, array('id' => $id));
            } else {
				$params[$id]['tmp_id'] = $id;
                $newId = self::$tableObj->insert($data);
            }
			$params[$id]['id'] = $newId;
			$params[$id]['params'] = utilsGmp::unserialize($data['params']);
        }
        return $params;
    }*/
    /*public function updateMarker($marker){
        $insert = array(
			'marker_group_id'   =>  $marker['goup_id'],
			'title'             =>  $marker['title'],
			'address'           =>  $marker['address'],
			'description'       =>  $marker['desc'],
			'coord_x'           =>  $marker['position']['coord_x'],
			'coord_y'           =>  $marker['position']['coord_y'],
			'animation'         =>  $marker['animation'],
			'icon'              =>  $marker['icon']['id'],
			'params'			=>  utilsGmp::serialize(array('titleLink' => $marker['titleLink']))
		);
		return self::$tableObj->update($insert," `id`='".$marker['id']."'");
    }*/
    public function getMapMarkers($mapId, $withGroup = false, $userId = false) {
		$mapId = (int) $mapId;
		$params = array('map_id' => $mapId);
		if($userId) {
			$params['user_id'] = $userId;
		}
        $markers = frameGmp::_()->getTable('marker')->orderBy('sort_order ASC')->get('*', $params);
		if(!empty($markers)) {
			$iconIds = array();
			foreach($markers as $i => $m) {

				$markers[$i] = $this->_afterGet($markers[$i], false, true);
				// We need to do shortcode only on frontend
				if(isset($markers[$i]["description"])){
					ob_start();
					echo do_shortcode($markers[$i]["description"]);
					$out = ob_get_contents();
					ob_end_clean();
					$markers[$i]["description"] = $out;
				}
				$iconIds[ $m['icon'] ] = 1;
			}
			$usedIcons = frameGmp::_()->getModule('icons')->getModel()->getIconsByIds( array_keys($iconIds) );
			foreach($markers as $i => $m) {
				$markers[$i]['icon_data'] = isset($usedIcons[ $m['icon'] ]) ? $usedIcons[ $m['icon'] ] : '' ;
			}
		}
        return $markers;
    }
	public function getMapMarkersIds($mapId) {
		return frameGmp::_()->getTable('marker')->get('id', array('map_id' => $mapId), '', 'col');
	}
	public function getMarkersByIds($ids) {
		if(!is_array($ids))
			$ids = array( $ids );
		$ids = array_map('intval', $ids);
		$markers = frameGmp::_()->getTable('marker')->get('*', array('additionalCondition' => 'id IN ('. implode(',', $ids). ')'));
		if(!empty($markers)) {
			foreach($markers as $i => $m) {
				$markers[$i] = $this->_afterGet($markers[$i]);
			}
		}
		return $markers;
	}
    public function removeMarker($markerId){
		dispatcherGmp::doAction('beforeMarkerRemove', $markerId);
		return frameGmp::_()->getTable('marker')->delete(array('id' => $markerId));
    }
	public function removeList($ids) {
		$ids = array_map('intval', $ids);
		return frameGmp::_()->getTable('marker')->delete(array('additionalCondition' => 'id IN ('. implode(',', $ids). ')'));
	}
    public function findAddress($params){
        if(!isset($params['addressStr']) || strlen($params['addressStr']) < 3){
            $this->pushError(__('Address is empty or not match', GMP_LANG_CODE));
            return false;
        }
        $addr = $params['addressStr'];
        $getdata = http_build_query(
            array(
                'address' => $addr,
                'language' => 'en',
				'sensor'=>'false',
			)
		);
		$apiDomain = frameGmp::_()->getModule('gmap')->getView()->getApiDomain();
        $google_response = utilsGmp::jsonDecode(file_get_contents($apiDomain . 'maps/api/geocode/json?'. $getdata));
        $res = array();
        foreach($google_response['results'] as $response) {
            $res[] = array(
				'position'  =>  $response['geometry']['location'],
				'address'   =>  $response['formatted_address'],
            );
        }
        return $res;
    }
    public function removeMarkersFromMap($mapId){
        return frameGmp::_()->getTable('marker')->delete("`map_id`='".$mapId."'");
    }
    public function getAllMarkers($d = array(), $widthMapData = false) {
		if(isset($d['limitFrom']) && isset($d['limitTo']))
			frameGmp::_()->getTable('marker')->limitFrom($d['limitFrom'])->limitTo($d['limitTo']);
		if(isset($d['orderBy']) && !empty($d['orderBy'])) {
			frameGmp::_()->getTable('marker')->orderBy( $d['orderBy'] );
		}
        $markerList = frameGmp::_()->getTable('marker')->get('*', $d);
        $iconsModel = frameGmp::_()->getModule('icons')->getModel();
        foreach($markerList as $i => &$m) {
			$markerList[$i] = $this->_afterGet($markerList[$i], $widthMapData);
        } 
        return $markerList;
    }
	public function setMarkersToMap($addMarkerIds, $mapId) {
		if(!is_array($addMarkerIds))
			$addMarkerIds = array($addMarkerIds);
		$addMarkerIds = array_map('intval', $addMarkerIds);
		return frameGmp::_()->getTable('marker')->update(array('map_id' => (int)$mapId), array('additionalCondition' => 'id IN ('. implode(',', $addMarkerIds). ')'));
	}
	public function getCount($d = array()) {
		return frameGmp::_()->getTable('marker')->get('COUNT(*)', $d, '', 'one');
	}
	public function updatePos($d = array()) {
		$d['id'] = isset($d['id']) ? (int) $d['id'] : 0;
		if($d['id']) {
			return frameGmp::_()->getTable('marker')->update(array(
				'coord_x' => $d['lat'],
				'coord_y' => $d['lng'],
			), array(
				'id' => $d['id'],
			));
		} else
			$this->pushError (__('Invalid Marker ID'));
		return false;
	}
	public function replaceDeletedIconIdToDefault($id){
		if($id) {
			return frameGmp::_()->getTable('marker')->update(array(
				'icon' => '1',
			), array(
				'icon' => $id,
			));
		} else
			$this->pushError (__('Invalid ID'));
		return false;
	}
}