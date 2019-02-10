<?php
class tableMapsGmp extends tableGmp{
    public function __construct() {

        $this->_table = '@__maps';
        $this->_id = 'id';
        $this->_alias = 'toe_m';
        $this->_addField('id', 'int', 'int', '11')
                ->_addField('title', 'varchar', 'varchar', '255')
                ->_addField('description', 'text', 'text', '')
                ->_addField('html_options', 'text', 'text', '')
                ->_addField('create_date', 'datetime', 'datetime', '')
                ->_addField('params', 'text', 'text', '');

    }
}

