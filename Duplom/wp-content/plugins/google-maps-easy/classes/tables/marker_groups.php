<?php
class tableMarker_groupsGmp extends tableGmp{
    public function __construct() {

        $this->_table = '@__marker_groups';
        $this->_id = 'id';
        $this->_alias = 'gmp_mrgr';
        $this->_addField('id', 'int', 'int', '11', '')
                ->_addField('title', 'varchar', 'varchar', '255', '')
                ->_addField('description', 'text', 'text', '', '')
				->_addField('params', 'text', 'text', '', '')
				->_addField('parent','int','int','0', '')
				->_addField('sort_order','int','int','0', '');
    }
}

