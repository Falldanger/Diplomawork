<?php
class  markerGmp extends moduleGmp {
	public function init() {
		//dispatcherGmp::addFilter('adminOptionsTabs', array($this, 'addOptionsTab'));
		//dispatcherGmp::addAction('tplHeaderBegin',array($this,'showFavico'));
		//dispatcherGmp::addAction('tplBodyEnd',array($this, 'GoogleAnalitics'));
		//dispatcherGmp::addAction('in_admin_footer',array($this, 'showPluginFooter'));
	}
	/*public function addOptionsTab($tabs){
		if(frameGmp::_()->isAdminPlugPage()){
//			frameGmp::_()->addScript('adminMetaOptions',$this->getModPath().'js/admin.marker.js',array(),false,true);
		}
		return $tabs;
	}*/
	/*public function connectAssets() {
		frameGmp::_()->addScript('marker', $this->getModPath(). 'js/marker.js');
	}*/
	public function getAnimationList() {
		return array(
			0 => __('None', GMP_LANG_CODE),
			1 => __('Drop', GMP_LANG_CODE),	//DROP
			2 => __('Bounce', GMP_LANG_CODE),	//BOUNCE
		);
	}
}