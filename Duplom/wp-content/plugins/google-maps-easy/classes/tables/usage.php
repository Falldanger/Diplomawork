<?php
class tableUsage_statGmp extends tableGmp{
    public function __construct() {

        $this->_table = '@__usage_stat';
        $this->_id = 'id';
        $this->_alias = 'gmp_icons';
        $this->_addField('id', 'int', 'int', '11', __('Usage id', GMP_LANG_CODE))
               ->_addField('code', 'varchar', 'varchar', '200', __('Code', GMP_LANG_CODE))
               ->_addField('visits', 'int', 'int', '11', __('Visits Count', GMP_LANG_CODE));
    }
}

