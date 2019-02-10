<?php
class marker_groupsModelGmp extends modelGmp {
	function __construct() {
		$this->_setTbl('marker_groups');
	}
	public function getAllMarkerGroups($d = array()){
		if(isset($d['limitFrom']) && isset($d['limitTo']))
			frameGmp::_()->getTable('marker_groups')->limitFrom($d['limitFrom'])->limitTo($d['limitTo']);

		$sortOrder = (isset($d['orderBy']) && !empty($d['orderBy'])) ? $d['orderBy'] : 'sort_order';

		$markerGroups = frameGmp::_()->getTable('marker_groups')->orderBy($sortOrder)->get('*', $d);
		$markerGroups = $this->_afterGet($markerGroups);

		return $markerGroups;
	}
	public function getMarkerGroupsByIds($ids){
		if(!$ids){
			return false;
		}
		if(!is_array($ids))
			$ids = array( $ids );
		$ids = array_map('intval', $ids);
		$groups = frameGmp::_()->getTable('marker_groups')->orderBy('sort_order')->get('*', array('additionalCondition' => 'id IN (' . implode(',', $ids) . ')'));
		$groups = $this->_afterGet($groups);
		if(!empty($groups)) {
			return $groups;
		}
		return false;
	}
	public function getMarkerGroupById($id = false){
		if(!$id){
			return false;
		}
		$markerGroup = frameGmp::_()->getTable('marker_groups')->orderBy('sort_order')->get('*', array('id' => (int)$id), '', 'row');
		$markerGroup = $this->_afterGet($markerGroup, true);
		if(!empty($markerGroup)){
			return $markerGroup;
		}
		return false;
	}
	public function remove($markerGroupId){
		$markerGroupId = (int) $markerGroupId;
		if(!empty($markerGroupId)) {
			//After category with parent was deleted, connect it to "parent parent" category
			$groupToRemoveData = array_shift($this->getMarkerGroupsByIds($markerGroupId));
			$parentGroupToRemove = $groupToRemoveData['parent'];
			$groupChildren = frameGmp::_()->getTable('marker_groups')->get('*', array('parent' => (int)$markerGroupId), '', 'row');
			if(!empty($groupChildren)){
				frameGmp::_()->getTable("marker_groups")->update(array('parent'=>$parentGroupToRemove), array('id'=>$groupChildren['id']));
			}
			$deleteMarkerGroup = frameGmp::_()->getTable("marker_groups")->delete($markerGroupId);
			if($deleteMarkerGroup){
				return frameGmp::_()->getTable("marker")->update(array('marker_group_id' => 0), array('marker_group_id' => $markerGroupId));
			}
		} else
			$this->pushError (__('Invalid ID', GMP_LANG_CODE));
		return false;
	}
	protected function _afterGet($data, $single = false) {
		if($single) {
			$data = array($data);
		}
		foreach($data as $k => $group) {
			$data[$k]['params'] = utilsGmp::unserialize($data[$k]['params']);
			// try to find parent element, if no parent set parent = 0
			// its for correct display category in view
			if($data[$k]['parent'] === $data[$k]['id']){
				$data[$k]['parent'] = 0;
			}
			//the first group should always be without a parent
			if($data[$k]['sort_order'] == 1){
				$data[$k]['parent'] = 0;
			}
			if(!$single && $this->searchForId($data[$k]['parent'], $data) === false){
				$data[$k]['parent'] = 0;
			}
		}
		if($single) {
			$data = $data[0];
		}
		return $data;
	}
	public function searchForId($id, $array) {
		foreach ($array as $key => $val) {
			if ($val['id'] === $id) {
				return $key;
			}
		}
		return false;
	}
	protected function _dataSave($data, $update = false) {
		$data['title'] = trim($data['title']);

		$mgrParamsKeys = array('bg_color', 'claster_icon', 'claster_icon_width', 'claster_icon_height');
		$mgrParams = array();
		foreach($mgrParamsKeys as $k){
			$mgrParams[$k] = isset($data[$k]) ? $data[$k] : null;
		}
		$data['params'] = utilsGmp::serialize($mgrParams);

		return $data;
	}
	private function _validateSaveMarkerGroup($markerGroup) {
		if(empty($markerGroup['title'])) {
			$this->pushError(__('Please enter Marker Category'), 'marker_group[title]', GMP_LANG_CODE);
		}
		return !$this->haveErrors();
	}
	public function updateMarkerGroup($params){
		$data = $this->_dataSave($params);
		if($this->_validateSaveMarkerGroup($data)) {
			$res = frameGmp::_()->getTable('marker_groups')->update($data, array('id' => (int)$params['id']));
			return $res;
		}
		return false;
	}
	public function updateMarkerGroupParent($id, $parentId){
		return frameGmp::_()->getTable('marker_groups')->update(array('parent' => $parentId), array('id' => $id));
	}
	public function saveNewMarkerGroup($params){
		if(!empty($params)) {
			$insertData = $this->_dataSave($params);
			if($this->_validateSaveMarkerGroup($insertData)) {
				$newMarkerGroupId = frameGmp::_()->getTable('marker_groups')->insert($insertData);
				if($newMarkerGroupId){
					$allMarkerGroups = $this->getAllMarkerGroups();
					$allMarkerGroupsIds = array();

					foreach($allMarkerGroups as $g) {
						if($g['id'] != $newMarkerGroupId) {
							array_push($allMarkerGroupsIds, $g['id']);
						}
					}
					$elem = $this->getNewMarkerGroupSortOrder($allMarkerGroups, $newMarkerGroupId, $insertData['parent']);
					$offset = array_search($elem, $allMarkerGroupsIds);
					array_splice($allMarkerGroupsIds, ($offset + 1), 0, array((string)$newMarkerGroupId));
					$this->resortMarkerGroups($allMarkerGroupsIds);
					return $newMarkerGroupId;
				} else {
					$this->pushError(frameGmp::_()->getTable('marker_groups')->getErrors());
				}
			}
		} else
			$this->pushError(__('Empty Params', GMP_LANG_CODE));
		return false;
	}
	public function getNewMarkerGroupSortOrder($allMarkerGroups, $newMarkerGroupId, $parent) {
		$allChildren = $this->getChildrenList($allMarkerGroups, $parent);
		unset($allChildren[array_search($newMarkerGroupId, $allChildren)]);
		$allChildren = array_values($allChildren);
		return !empty($allChildren) ? $allChildren[count($allChildren) - 1] : $parent;
	}
	public function getChildrenList($groups, $parent, $children = array()) {
		foreach($groups as $g) {
			if($g['parent'] == $parent) {
				array_push($children, $g['id']);
				$children = $this->getChildrenList($groups, $g['id'], $children);
			}
		}
		return $children;
	}
	public function resortMarkerGroups($markerGroupsIds = array()) {
		if($markerGroupsIds) {
			$i = 1;
			foreach($markerGroupsIds as $mgrId) {
				frameGmp::_()->getTable('marker_groups')->update(array('sort_order' => $i++), array('id' => $mgrId));
			}
		}
		return true;
	}
	public function getCurrentMapMarkersGroupsTree($map, $withUncategorized = false) {
		$uncategorized = $withUncategorized ? $this->getUncategorizedGroupData() : array();
		$markerGroupsTree = array();

		if(isset($map['markers']) && !empty($map['markers'])) {
			$groupsForCurMap = array();

			foreach($map['markers'] as $marker) {
				if(is_array($marker['marker_group_ids'])){
					foreach ($marker['marker_group_ids'] as $marker_group_id) {
						array_push($groupsForCurMap, $marker_group_id);
					}
				}
			}
			$markerGroups = $this->getMarkerGroupsByIds($groupsForCurMap);

			if(!empty($markerGroups)) {
				$markerGroups = $this->updateMarkerGroupsListByParents($markerGroups, $groupsForCurMap);
				$markerGroupsTree = $this->getMarkerGroupsTree($markerGroups);
				$markerGroupsTree = array_merge($markerGroupsTree, $uncategorized);	// should be in the end of list
			} else {
				$markerGroupsTree = $uncategorized;
			}
		}
		return $markerGroupsTree;
	}
	public function getMarkerGroupsTree($groups, $parentId = 0) {
		$res = array();
		foreach($groups as $i => $g) {
			if(isset($g['parent']) && $g['parent'] == $parentId) {
				$res[] = array_merge($g, array('children' => $this->getMarkerGroupsTree($groups, $g['id'])));
			}
		}
		return $res;
	}
	public function updateMarkerGroupsListByParents($groups, $groupsIds) {
		foreach($groups as $i => $g) {
			if(isset($g['parent']) && $g['parent'] > 0 && !in_array($g['parent'], $groupsIds)) {
				array_push($groups, $this->getMarkerGroupById($g['parent']));
				array_push($groupsIds, $g['parent']);
				return $this->updateMarkerGroupsListByParents($groups, $groupsIds);
			}
		}
		return $groups;
	}
	public function getUncategorizedGroupData() {
		return array(array(
			'id' => 0,
			'parent' => 0,
			'title' => __('Uncategorized', GMP_LANG_CODE),	// group for uncategorized markers
			'params' => '',
			'markers' => array(),
		));
	}
	public function getMarkerGroupsForSelect($markerGroupsForSelect = array()) {
		$allMarkerGroupsList = $this->getAllMarkerGroups();

		foreach($allMarkerGroupsList as $key => $value) {
			$markerGroupsForSelect[ $value['id'] ] = $this->_updateTitleForTreeView($value['title'], $value, $allMarkerGroupsList);
		}
		return $markerGroupsForSelect;
	}
	public function _updateTitleForTreeView($title, $group, $allMarkerGroups) {
		$level = $this->_itemGetLevel($group, $allMarkerGroups);
		$title = str_repeat('-', $level) . ' ' . $title;
		return $title;
	}
	public function _itemGetLevel($group, $allMarkerGroups, $level = 0) {
		if($group['parent'] != 0) {
			foreach($allMarkerGroups as $g) {
				if($g['id'] == $group['parent'] && $level < 10) {
					$level = $this->_itemGetLevel($g, $allMarkerGroups, ++$level);
				}
			}
		}
		return $level;
	}
}