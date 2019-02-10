<?php
class  iconsGmp extends moduleGmp {
	public function init(){
		parent::init();
		add_filter('upload_mimes', array($this, 'addMimeTypes'));
		$this->getModel()->checkDefIcons();
		/*if(frameGmp::_()->isAdminPlugPage()){
			$gmpExistsIcons = $this->getModel()->getIcons();
			frameGmp::_()->addJSVar('iconOpts', 'gmpExistsIcons', $gmpExistsIcons);
			frameGmp::_()->addScript('iconOpts', $this->getModPath() .'js/iconOpts.js');			
		}*/
	}
	function addMimeTypes($mimes) {
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}
}