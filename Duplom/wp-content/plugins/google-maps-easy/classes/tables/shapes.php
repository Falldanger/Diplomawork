<?php
class tableShapeGmp extends tableGmp{
    public function __construct() {
        $this->_table = '@__shapes';
        $this->_id = 'id';
        $this->_alias = 'toe_shp';
        $this->_addField('id', 'int', 'int', '11', __('Shape ID', GMP_LANG_CODE))
                ->_addField('title', 'varchar', 'varchar', '255', __('Shape name', GMP_LANG_CODE))
                ->_addField('description', 'text', 'text', '', __('Description of Shape', GMP_LANG_CODE))
                ->_addField('coords', 'text', 'text', '', __('Shape coordinates list', GMP_LANG_CODE))
                ->_addField('type', 'varchar', 'varchar', '30', __('Shape type', GMP_LANG_CODE))
                ->_addField('map_id', 'int', 'int', '11', __('Map Id', GMP_LANG_CODE))
				->_addField('create_date','text','text','',  __('Creation date', GMP_LANG_CODE))
				->_addField('animation','int','int','0', __('Animation', GMP_LANG_CODE))
                ->_addField('params','text','text','', __('Params', GMP_LANG_CODE))
				->_addField('sort_order','int','int','0', __('Sort Order', GMP_LANG_CODE));
    }
}