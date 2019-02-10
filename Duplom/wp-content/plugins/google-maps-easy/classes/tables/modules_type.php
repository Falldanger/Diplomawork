<?php
class tableModules_typeGmp extends tableGmp {
    public function __construct() {
        $this->_table = '@__modules_type';
        $this->_id = 'id';     /*Let's associate it with posts*/
        $this->_alias = 'toe_m_t';
        $this->_addField($this->_id, 'text', 'int', '', __('ID', GMP_LANG_CODE))->
                _addField('label', 'text', 'varchar', '', __('Label', GMP_LANG_CODE), 128);
    }
}
?>
