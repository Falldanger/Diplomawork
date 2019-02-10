<?php
class marker_groups_relationModelGmp extends modelGmp {
	function __construct() {
		$this->_setTbl('marker_groups_relation');
	}

	public function getRelationsByMarkerId($id){
//		$relations =  frameGmp::_()->getTable('marker_groups_relation')->get('groups_id', 'marker_id = ' . $id, '', 'col');
		return frameGmp::_()->getTable('marker_groups_relation')->get('groups_id', 'marker_id = ' . $id, '', 'col');
	}

}
