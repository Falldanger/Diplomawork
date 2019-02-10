<?php
class tableHeatmapGmp extends tableGmp{
	public function __construct() {
		$this->_table = '@__heatmaps';
		$this->_id = 'id';
		$this->_alias = 'toe_hmp';
		$this->_addField('id', 'int', 'int', '11', __('Heatmap ID', GMP_LANG_CODE))
			->_addField('map_id', 'int', 'int', '11', __('Map Id', GMP_LANG_CODE))
			->_addField('coords', 'text', 'text', '', __('Heatmap coordinates list', GMP_LANG_CODE))
			->_addField('params','text','text','', __('Params', GMP_LANG_CODE))
			->_addField('create_date','text','text','',  __('Creation date', GMP_LANG_CODE));
	}
}