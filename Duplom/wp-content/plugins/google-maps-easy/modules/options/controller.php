<?php
class optionsControllerGmp extends controllerGmp {
	public function saveGroup() {
		$res = new responseGmp();
		if($this->getModel()->saveGroup(reqGmp::get('post'))) {
			$res->addMessage(__('Done', GMP_LANG_CODE));
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			GMP_USERLEVELS => array(
				GMP_ADMIN => array('saveGroup')
			),
		);
	}
}

