<?php
class tableIconsGmp extends tableGmp{
    public function __construct() {

        $this->_table = '@__icons';
        $this->_id = 'id';
        $this->_alias = 'gmp_icons';
        $this->_addField('id', 'int', 'int', '11')
                ->_addField('title', 'varchar', 'varchar', '100')
                ->_addField('description', 'description', 'text', '')
                ->_addField('path', 'varchar', 'varchar', '255');
    }
}

