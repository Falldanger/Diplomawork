<?php
class membership_presetsModelGmp extends modelGmp {
	private $_memberShipClassName;

	function __construct() {
		$this->_memberShipClassName = 'SupsysticMembership';
		$this->_setTbl('membership_presets');
	}

	public function isPluginActive() {
		$tableExistsQuery =  "SHOW TABLES LIKE '@__" . $this->_tbl . "'";
		$results = dbGmp::get($tableExistsQuery);

		if(class_exists($this->_memberShipClassName)) {
			if(count($results)) {
				return true;
			} else {
				return false;
			}
		}
		return null;
	}

	public function getPluginInstallUrl() {
		return add_query_arg(
			array(
				's' => 'Membership by Supsystic',
				'tab' => 'search',
				'type' => 'term',
			),
			admin_url( 'plugin-install.php' )
		);
	}

	public function getPluginInstallWpUrl() {
		return 'https://wordpress.org/plugins/membership-by-supsystic/';
	}

	public function updateRow($params) {

		if(isset($params['maps_id']) && isset($params['allow_use'])) {
			$allowUse = (int)$params['allow_use'];
			$mapsId = (int)$params['maps_id'];

			$query = "INSERT INTO `@__" . $this->_tbl . "`(`maps_id`, `allow_use`)"
				. " VALUES (" . $mapsId . ", " . $allowUse . ") "
				. "ON DUPLICATE KEY UPDATE `allow_use`=" . $allowUse;
			$res = dbGmp::query($query);
			return $res;
		}
		return false;
	}
	
	public function getEmuledMarkerInfo($markerInfo, $params) {
		$retArr = array(
			'id' => null,
			'title' => null,
			'description' => null,
			'coord_x' => null,
			'coord_y' => null,
			'icon' => null,
			'map_id' => 1,
			'marker_group_id' => 0,
			'address' => '',
			'animation' => null,
			'create_date' => null,
			'params' => array(
				'marker_link_src' => null,
				'marker_list_def_img_url' => null,
				'title_is_link' => null,
			),
			'sort_order' => 1,
			'user_id' => null,
			'icon_data' => array(
				'id' => null,
				'title' => 'marker',
				'description' => 'blue,white,star,pin',
				'path' => 'http://sst-w1.loc/wp-content/plugins/google-maps-easy/modules/icons/icons_files/def_icons/marker.png',
			),
		);

		if(!empty($markerInfo['id'])) {
			$retArr['id'] = $markerInfo['id'];
		}
		if(!empty($markerInfo['title'])) {
			$retArr['title'] = $markerInfo['title'];
		}
		if(!empty($markerInfo['description'])) {
			$retArr['description'] = $markerInfo['description'];
		}
		if(!empty($markerInfo['lat'])) {
			$retArr['coord_x'] = $markerInfo['lat'];
		}
		if(!empty($markerInfo['lng'])) {
			$retArr['coord_y'] = $markerInfo['lng'];
		}

		if(!empty($markerInfo['address'])) {
			$retArr['address'] = $markerInfo['address'];
		}

		if(isset($markerInfo['iconId']) && $markerInfo['iconId']) {
			$retArr['icon_data']['id'] = $markerInfo['iconId'];
			$retArr['icon'] = $markerInfo['iconId'];
		}
		if(!empty($markerInfo['iconUrl'])) {
			$retArr['icon_data']['path'] = $markerInfo['iconUrl'];
		}
		if(!empty($markerInfo['iconTitle'])) {
			$retArr['icon_data']['title'] = $markerInfo['iconTitle'];
		}

		$retArr['map_id'] = $params['id'];
		return $retArr;
	}
	
	public function prepareParamsWithMarkers($params) {
		if(!empty($params['membership-params']['markers']) && !empty($params['iconsList']) && count($params['iconsList'])) {
			foreach($params['membership-params']['markers'] as $markerKey => $markerValue) {

				foreach($params['iconsList'] as $iconElem) {
					if(!empty($iconElem['id']) && $iconElem['id'] == $markerValue['iconId']) {
						$params['membership-params']['markers'][$markerKey]['iconUrl'] = $iconElem['path'];
						$params['membership-params']['markers'][$markerKey]['iconTitle'] = $iconElem['title'];
					}
				}
			}
		}
		return $params;
	}

	public function replaceMapsParamsForMembership(&$mapObj, array $params) {
		if(isset($params['membership-params']['center']['lat']) && isset($params['membership-params']['center']['lng'])) {
			$mapObj['params']['map_center'] = array(
				'coord_x' => $params['membership-params']['center']['lat'],
				'coord_y' => $params['membership-params']['center']['lng'],
			);
		}
		if(isset($params['membership-params']['zoom'])) {
			$mapObj['params']['zoom'] = $params['membership-params']['zoom'];
		}

		if(isset($params['membership-params']['markers']) && count($params['membership-params']['markers'])) {
			foreach($params['membership-params']['markers'] as $markerValue) {
				$mapObj['markers'][] = $this->getEmuledMarkerInfo($markerValue, $params);
			}
		}
	}
}