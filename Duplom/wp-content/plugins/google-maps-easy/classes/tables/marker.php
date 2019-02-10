<?php
class tableMarkerGmp extends tableGmp{
    public function __construct() {
        $this->_table = '@__markers';
        $this->_id = 'id';
        $this->_alias = 'toe_mr';
        $this->_addField('id', 'int', 'int', '11', __('Marker ID', GMP_LANG_CODE))
                ->_addField('title', 'varchar', 'varchar', '255', __('Marker name', GMP_LANG_CODE))
                ->_addField('description', 'text', 'text', '', __('Description Of Marker', GMP_LANG_CODE))
                ->_addField('coord_x', 'varchar', 'varchar', '50', __('X coordinate of marker (lng)', GMP_LANG_CODE))
                ->_addField('coord_y', 'varchar', 'varchar', '50', __('Y coordinate of marker (lat)', GMP_LANG_CODE))
                ->_addField('icon', 'varchar', 'varchar', '255', __('Path of icon file', GMP_LANG_CODE))
                ->_addField('map_id', 'int', 'int', '11', __('Map Id', GMP_LANG_CODE))                
                ->_addField('address', 'text', 'text', '', __('Marker Address', GMP_LANG_CODE))                
                ->_addField('marker_group_id', 'int', 'int', '11', __("Id of Marker's group", GMP_LANG_CODE))
                ->_addField('animation','int','int','0', __('Animation', GMP_LANG_CODE))
                ->_addField('params','text','text','', __('Params', GMP_LANG_CODE))
				->_addField('sort_order','int','int','0', __('Sort Order', GMP_LANG_CODE))
                ->_addField('create_date','datetime','datetime','',  __('Creation date', GMP_LANG_CODE))
				->_addField('period_from', 'datetime', 'datetime', null, __('Period date from', GMP_LANG_CODE))
				->_addField('period_to', 'datetime', 'datetime', null, __('Period date to', GMP_LANG_CODE))
				->_addField('hash', 'varchar', '32', __('Import Kml Layer unique index', GMP_LANG_CODE))
                ->_addField('user_id','int','int','11',  __('User who created marker', GMP_LANG_CODE));
    }
}

