<?php
class gmapControllerGmp extends controllerGmp {
	/*public function getAllMaps($withMarkers = false){
	   $maps = $this->getModel()->getAllMaps($withMarkers);
	   var_dump($maps);
	   return $maps;
	}*/
	protected function _prepareTextLikeSearch($val) {
		$query = '(title LIKE "%'. $val. '%"';
		if(is_numeric($val)) {
			$query .= ' OR id LIKE "%'. (int) $val. '%"';
		}
		$query .= ')';
		return $query;
	}
	public function save() {
		$saveRes = false;
		$data = reqGmp::get('post');
		$res = new responseGmp();
		$mapId = 0;
		$edit = true;
		if(!isset($data['map_opts'])) {
			$res->pushError(__('Map data not found', GMP_LANG_CODE));
			return $res->ajaxExec();
		}
		if(isset($data['map_opts']['id']) && !empty($data['map_opts']['id'])) {
			$saveRes = $this->getModel()->updateMap($data['map_opts']);
			$mapId = $data['map_opts']['id'];
		} else {
			$saveRes = $this->getModel()->saveNewMap($data['map_opts']);
			$mapId = $saveRes;
			$edit = false;
		}
		if($saveRes) {
			// save Membership param
			$membershipModule = frameGmp::_()->getModule('membership');
			if($membershipModule) {
				$membershipModel = $membershipModule->getModel('membership_presets');
				if($membershipModel) {
					$membershipModel->updateRow(array('maps_id' => $mapId, 'allow_use' => isset($data['map_opts']['membershipEnable']) ? $data['map_opts']['membershipEnable'] : 0));
				}
			}
			$addMarkerIds = reqGmp::getVar('add_marker_ids');
			if($addMarkerIds && !empty($addMarkerIds)) {
				frameGmp::_()->getModule('marker')->getModel()->setMarkersToMap($addMarkerIds, $mapId);
				$this->getModel()->resortMarkers(array('map_id' => $mapId));
			}
			if(frameGmp::_()->getModule('supsystic_promo')->isPro()) {
				$addShapeIds = reqGmp::getVar('add_shape_ids');
				if($addShapeIds && !empty($addShapeIds) && frameGmp::_()->getModule('shape')) {
					frameGmp::_()->getModule('shape')->getModel()->setShapesToMap($addShapeIds, $mapId);
					$this->getModel()->resortShapes(array('map_id' => $mapId));
				}
			}
			$res->addMessage(__('Done', GMP_LANG_CODE));
			$res->addData('map_id', $mapId);
			$res->addData('map', $this->getModel()->getMapById( $mapId ));
			if(!$edit) {	// For new maps
				$fullEditUrl = $this->getModule()->getEditMapLink( $mapId );
				$editUrlParts = explode('/', $fullEditUrl);
				$res->addData('edit_url', $editUrlParts[ count($editUrlParts) - 1 ]);
			}
		} else {
			$res->pushError( $this->getModel()->getErrors() );
		}
		//frameGmp::_()->getModule('supsystic_promo')->getModel()->saveUsageStat('map.edit');
		return $res->ajaxExec();
	}
	public function remove() {
		$res = new responseGmp();
		if($this->getModel()->remove(reqGmp::getVar('id', 'post'))) {
			$res->addMessage(__('Done', GMP_LANG_CODE));
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function cloneMapGroup() {
		$res = new responseGmp();
		if($this->getModel()->cloneMapGroup(reqGmp::getVar('listIds', 'post'))) {
			$res->addMessage(__('Done', GMP_LANG_CODE));
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	/*public function removeMap(){
		$data=  reqGmp::get('post');
		$res = new responseGmp();
		if(!isset($data['map_id']) || empty($data['map_id'])){
			$res->pushError(__("Nothing to remove", GMP_LANG_CODE));
			return $res->ajaxExec();
		}

		if($this->getModel()->remove($data['map_id'])){
			$res->addMessage(__("Done", GMP_LANG_CODE));
		}else{
			$res->pushError($this->getModel()->getErrors());
		}
		frameGmp::_()->getModule("supsystic_promo")->getModel()->saveUsageStat("map.delete");
		return $res->ajaxExec();
	}*/

	/*public function getListForTable() {
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
		$list = $this->getModel()->getAllMaps( $listReqData, true );

		$res->addData('aaData', $this->_convertDataForDatatable($list));
		$res->addData('iTotalRecords', $count);
		$res->addData('iTotalDisplayRecords', $count);
		$res->addData('sEcho', reqGmp::getVar('sEcho'));
		$res->addMessage(__('Done'));
		return $res->ajaxExec();
	}*/

	/*public function getMapById()
	{
		$res = new responseGmp();

	    $req = reqGmp::get('post');

		if (!isset($req['id']) || 1 > (int)$req['id']) {
			$res->pushError(__('Invalid map identifier.', GMP_LANG_CODE));

			return $res->ajaxExec();
		}
		$model = $this->getModel();
		$map = $model->getMapById($req['id']);

		if (!$map) {
			$res->pushError(__('Failed to find map.', GMP_LANG_CODE));

			return $res->ajaxExec();
		}

		$res->addData('map', (array)$map);

		return $res->ajaxExec();
	}*/

	protected function _prepareListForTbl($data) {
		if (!empty($data)) {
			foreach($data as $i => $v) {
				$mapId   = (int)$data[$i]['id'];
				$map     = $this->getModel()->getMapById($mapId);

				// Pretty date format based on the WordPress options
				$format = get_option('date_format');
				$createDate = date($format, strtotime($data[$i]['create_date']));

				// Markers
				$markers = $this->getView()->getListMarkers($map);

				// Actions
				$actions = $this->getView()->getListOperations($map);

				$data[$i]['create_date'] = $createDate;
				$data[$i]['markers'] = preg_replace('/\s+/', ' ', trim($markers));
				$data[$i]['actions'] = preg_replace('/\s\s+/', ' ', trim($actions));
			}
		}

		return $data;
	}
//	protected function _prepareTextLikeSearch($val) {
//		$query = '(ip LIKE "%'. $val. '%"';
//		if(is_numeric($val)) {
//			$query .= ' OR id LIKE "%'. (int) $val. '%"';
//		}
//		$query .= ')';
//		return $query;
//	}
//	protected function _prepareSortOrder($sortOrder) {
//		switch($sortOrder) {
//			case 'type_label':
//				$sortOrder = 'type';
//				break;
//		}
//		return $sortOrder;
//	}


	/*private function _convertDataForDatatable($list, $single = false) {
		$returnList = array();
		if($single) {
			$list = array($list);
		}
		foreach($list as $i => $map) {
			$returnList[ $i ] = $map;
			$returnList[ $i ]['list_html_options'] = $this->getView()->getListHtmlOptions( $map );
			$returnList[ $i ]['list_markers'] = $this->getView()->getListMarkers( $map );
			$returnList[ $i ]['operations'] = $this->getView()->getListOperations( $map );
		}
		if($single) {
			return $returnList[0];
		}
		return $returnList;
	}*/
	public function resortMarkers() {
		$res = new responseGmp();
		if(!$this->getModel()->resortMarkers(reqGmp::get('post'))) {
			$res->pushError( $this->getModel()->getErrors() );
		}
		return $res->ajaxExec();
	}
	public function resortShapes() {
		$res = new responseGmp();
		if(!$this->getModel()->resortShapes(reqGmp::get('post'))) {
			$res->pushError( $this->getModel()->getErrors() );
		}
		return $res->ajaxExec();
	}
	/**
	 * @see controller::getPermissions();
	 */
	public function getPermissions() {
		return array(
			GMP_USERLEVELS => array(
				GMP_ADMIN => array('getListForTbl', 'getAllMaps', 'save', 'clear', 'remove', 'removeGroup', 'cloneMapGroup', 'resortMarkers')
			),
		);
	}
} 