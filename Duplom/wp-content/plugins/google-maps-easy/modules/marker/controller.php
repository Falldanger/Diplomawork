<?php
class markerControllerGmp extends controllerGmp {
	public function save() {
		$res = new responseGmp();
		$markerData = reqGmp::getVar('marker_opts');
		$update = false;
		if($id = $this->getModel()->save($markerData, $update)){
			$res->addMessage(__('Done', GMP_LANG_CODE));
			$res->addData('marker', $this->getModel()->getById($id));
			$res->addData('update', $update);
		} else {
			$res->pushError($this->getModel()->getErrors());
		}
        //frameGmp::_()->getModule('supsystic_promo')->getModel()->saveUsageStat('marker.save');
        return $res->ajaxExec();
	}
	public function updatePos() {
		$res = new responseGmp();
		if($this->getModel()->updatePos(reqGmp::get('post'))) {
			//$res->addMessage(__('Done', GMP_LANG_CODE));	// Do nothing for now - void method
		} else {
			$res->pushError($this->getModel()->getErrors());
		}
        return $res->ajaxExec();
	}
    public function findAddress(){
        $data = reqGmp::get('post');
        $res = new responseGmp();
        $result = $this->getModel()->findAddress($data);
        if($result) {
            $res->addData($result);
        } else {
			$res->pushError($this->getModel()->getErrors());
        }
        //frameGmp::_()->getModule('supsystic_promo')->getModel()->saveUsageStat('geolocation.address.search');
        return $res->ajaxExec();
    }
    public function removeMarker(){
        $params = reqGmp::get('post');
        $res = new responseGmp();
        if(!isset($params['id'])){
            $res->pushError(__('Marker Not Found', GMP_LANG_CODE));
            return $res->ajaxExec();
        }    
        if($this->getModel()->removeMarker($params["id"])){
           $res->addMessage(__("Done", GMP_LANG_CODE)); 
        }else{
            $res->pushError(__("Cannot remove marker", GMP_LANG_CODE));
        }
        //frameGmp::_()->getModule("supsystic_promo")->getModel()->saveUsageStat('marker.delete');
        return $res->ajaxExec();
    }
	public function removeList() {
		$params = reqGmp::get('post');
        $res = new responseGmp();
        if(!isset($params['remove_ids'])){
			$res->pushError(__('Marker Not Found', GMP_LANG_CODE));
			return $res->ajaxExec();
        }
        if($this->getModel()->removeList($params['remove_ids'])){
           $res->addMessage(__('Done', GMP_LANG_CODE)); 
        } else {
            $res->pushError(__('Cannot remove markers', GMP_LANG_CODE));
        }
        //frameGmp::_()->getModule("supsystic_promo")->getModel()->saveUsageStat('marker.delete_list');
        return $res->ajaxExec();
	}
	public function getMarkerForm($params){
		return $this->getView()->getMarkerForm($params);
	}
	public function getListForTable() {
		$res = new responseGmp();
		$res->ignoreShellData();
		
		$count = $this->getModel()->getCount();
		$listReqData = array(
			'limitFrom' => reqGmp::getVar('iDisplayStart'),
			'limitTo' => reqGmp::getVar('iDisplayLength'),
		);
		$displayColumns = $this->getView()->getDisplayColumns();
		$displayColumnsKeys = array_keys($displayColumns);
		$iSortCol = reqGmp::getVar('iSortCol_0');
		if(!is_null($iSortCol) && is_numeric($iSortCol)) {
			$listReqData['orderBy'] = $displayColumns[ $displayColumnsKeys[ $iSortCol ] ]['db'];
			$iSortDir = reqGmp::getVar('sSortDir_0');
			if(!is_null($iSortDir)) {
				$listReqData['orderBy'] .= ' '. strtoupper($iSortDir);
			}
		}
		$search = reqGmp::getVar('sSearch');
		if(!is_null($search) && !empty($search)) {
			$dbSearch = dbGmp::escape($search);
			$listReqData['additionalCondition'] = 'title LIKE "%'. $dbSearch. '%" OR description LIKE "%'. $dbSearch. '%"';
		}
		$list = $this->getModel()->getAllMarkers( $listReqData, true );

		$res->addData('aaData', $this->_convertDataForDatatable($list));
		$res->addData('iTotalRecords', $count);
		$res->addData('iTotalDisplayRecords', $count);
		$res->addData('sEcho', reqGmp::getVar('sEcho'));
		$res->addMessage(__('Done'));
		return $res->ajaxExec();
	}
	public function getMapMarkers() {
		$res = new responseGmp();
		$mapId = (int) reqGmp::getVar('map_id', 'post');
		$markers = array();
		if($mapId) {
			$markers = $this->getModel()->getMapMarkers( $mapId );
		} else {
			$addedMarkerIds = reqGmp::getVar('added_marker_ids', 'post');
			if(!empty($addedMarkerIds)) {
				$markers = $this->getModel()->getMarkersByIds( $addedMarkerIds );
			}
		}
		if($markers !== false) {
			$res->addData('markers', $markers);
		} else
			$res->pushError($this->getModel ()->getErrors());
		return $res->ajaxExec();
	}
	private function _convertDataForDatatable($list) {
		foreach($list as $i => $marker) {
			$list[$i]['marker_check'] = htmlGmp::checkbox('marker_check['. $list[$i]['id']. ']');
			$list[$i]['list_icon'] = $this->getView()->getListIcon($list[$i]);
			$list[$i]['list_title'] = $this->getView()->getListTitle($list[$i]);
			$list[$i]['group_title'] = $list[$i]['marker_group']['title'];
			$list[$i]['list_address'] = $this->getView()->getListAddress($list[$i]);
			$list[$i]['uses_on_map'] = $this->getView()->getListUsesOnMap($list[$i]);
			$list[$i]['operations'] = $this->getView()->getListOperations($list[$i]);
		}
		return $list;
	}
	public function getMarker() {
		$res = new responseGmp();
		$id = (int) reqGmp::getVar('id');
		if($id) {
			$marker = $this->getModel()->getById($id);
			if(!empty($marker)) {
				$res->addData('marker', $marker);
			} else
				$res->pushError ($this->getModel()->getErrors());
		} else
			$res->pushError (__('Empty or invalid marker ID', GMP_LANG_CODE));
		return $res->ajaxExec();
	}
	protected function _prepareModelBeforeListSelect($model) {
		$map_id = (int) reqGmp::getVar('map_id');
		$model->addWhere(array('map_id' => $map_id));
		return $model;
	}
	protected function _prepareSortOrder($orderBy) {
		if(!$orderBy)
			$orderBy = 'sort_order';
		return $orderBy;
	}
	protected function _prepareListForTbl($data) {
		if (!empty($data)) {
			$markersIds = array('map_id' => $data[0]['map_id'], 'markers_list' => array());
			foreach($data as $i => $m) {
				$data[$i] = $this->getModel()->_afterGet($data[$i]);

				// Save Marker sort order
				$markersIds['markers_list'][] = $data[$i]['id'];

				// Marker Icon Image
				$icon = '<div class="egm-marker-icon"><img src="'. $data[$i]['icon_data']['path'] .'" /></div>';
				$data[$i]['icon_img'] = preg_replace('/\s\s+/', ' ', trim($icon));

				// Marker Coordinates
				$coords = '<div class="egm-marker-latlng">'
					. round($data[$i]['coord_x'], 2) . '"N '
					. round($data[$i]['coord_y'], 2) . '"E
					</div>';
				$data[$i]['coords'] = preg_replace('/\s\s+/', ' ', trim($coords));

				// Marker Action Buttons
				$data[$i]['actions'] = frameGmp::_()->getModule('marker')->getView()->getListOperations($data[$i]['id']);
			}
			frameGmp::_()->getModule('gmap')->getModel()->resortMarkers($markersIds);
		}

		return $data;
	}
	public function saveFindAddressStat() {
		//frameGmp::_()->getModule('supsystic_promo')->getModel()->saveUsageStat('geolocation.address.search');
	}
	/**
	 * @see controller::getPermissions();
	 */
	public function getPermissions() {
		return array(
			GMP_USERLEVELS => array(
				GMP_ADMIN => array('save', 'removeMarker', 'getMarkerForm', 'getListForTable', 'getMarker', 'removeList', 'getMapMarkers', 'updatePos')
			),
		);
	}
}