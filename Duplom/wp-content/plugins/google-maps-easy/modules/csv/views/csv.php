<?php
class csvViewGmp extends viewGmp {
	public function getTabContent() {
		$options = frameGmp::_()->getModule('options')->getModel()->getAll();
		$csv_options = !empty($options['csv_options']) && !empty($options['csv_options']['value']) ? $options['csv_options']['value'] : array();
		$this->assign('delimiters', array(';' => ';', ',' => ','));
		$this->assign('options', $csv_options);
		return parent::getContent('csvTabContent');
	}
}