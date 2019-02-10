<?php
class marker_groupsGmp extends moduleGmp {
	public function init(){
		dispatcherGmp::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
	}
	public function addAdminTab($tabs) {
		$tabs[ $this->getCode() ] = array(
			'label' => __('Marker Categories', GMP_LANG_CODE), 'callback' => array($this, 'getTabContent'), 'fa_icon' => 'fa-map-marker', 'sort_order' => 60,
		);
		$tabs[ $this->getCode(). '_add_new' ] = array(
			'label' => __('Add New', GMP_LANG_CODE), 'callback' => array($this, 'getAddNewTabContent'), 'sort_order' => 60, 'child_of' => $this->getCode(), 'hidden' => 1, 'add_bread' => $this->getCode(),
		);
		$tabs[ $this->getCode(). '_edit' ] = array(
			'label' => __('Edit', GMP_LANG_CODE), 'callback' => array($this, 'getEditTabContent'), 'sort_order' => 60, 'child_of' => $this->getCode(), 'hidden' => 1, 'add_bread' => $this->getCode(),
		);
		return $tabs;
	}
	public function getAddNewTabContent() {
		return $this->getView()->getEditMarkerGroup();
	}
	public function getEditTabContent() {
		$id = (int) reqGmp::getVar('id', 'get');
		if(!$id)
			return __('No Marker Category Found', GMP_LANG_CODE);
		return $this->getView()->getEditMarkerGroup( $id );
	}
	public function getTabContent() {
		return $this->getView()->getTabContent();
	}
	public function getEditMarkerGroupLink($id) {
		return frameGmp::_()->getModule('options')->getTabUrl('marker_groups_edit'). '&id='. $id;
	}
}