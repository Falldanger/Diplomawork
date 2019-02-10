<?php
abstract class controllerGmp {
	protected $_models = array();
	protected $_views = array();
	protected $_task = '';
	protected $_defaultView = '';
	protected $_code = '';
	public function __construct($code) {
		$this->setCode($code);
		$this->_defaultView = $this->getCode();
	}
	public function init() {
		/*load model and other preload data goes here*/
	}
	protected function _onBeforeInit() {

	}
	protected function _onAfterInit() {

	}
	public function setCode($code) {
		$this->_code = $code;
	}
	public function getCode() {
		return $this->_code;
	}
	public function exec($task = '') {
		if(method_exists($this, $task)) {
			$this->_task = $task;   //For multicontrollers module version - who know, maybe that's will be?))
			return $this->$task();
		}
		return null;
	}
	public function getView($name = '') {
		if(empty($name)) $name = $this->getCode();
		if(!isset($this->_views[$name])) {
			$this->_views[$name] = $this->_createView($name);
		}
		return $this->_views[$name];
	}
	public function getModel($name = '') {
		if(!$name)
			$name = $this->_code;
		if(!isset($this->_models[$name])) {
			$this->_models[$name] = $this->_createModel($name);
		}
		return $this->_models[$name];
	}
	protected function _createModel($name = '') {
		if(empty($name)) $name = $this->getCode();
		$parentModule = frameGmp::_()->getModule( $this->getCode() );
		$className = '';
		if(importGmp($parentModule->getModDir(). 'models'. DS. $name. '.php')) {
			$className = toeGetClassNameGmp($name. 'Model');
		}
		
		if($className) {
			$model = new $className();
			$model->setCode( $this->getCode() );
			return $model;
		}
		return NULL;
	}
	protected function _createView($name = '') {
		if(empty($name)) $name = $this->getCode();
		$parentModule = frameGmp::_()->getModule( $this->getCode() );
		$className = '';
		
		if(importGmp($parentModule->getModDir(). 'views'. DS. $name. '.php')) {
			$className = toeGetClassNameGmp($name. 'View');
		}
		
		if($className) {
			$view = new $className();
			$view->setCode( $this->getCode() );
			return $view;
		}
		return NULL;
	}
	public function display($viewName = '') {
		$view = NULL;
		if(($view = $this->getView($viewName)) === NULL) {
			$view = $this->getView();   //Get default view
		}
		if($view) {
			$view->display();
		}
	}
	public function __call($name, $arguments) {
		$model = $this->getModel();
		if(method_exists($model, $name))
			return $model->$name($arguments[0]);
		else
			return false;
	}
	/**
	 * Retrive permissions for controller methods if exist.
	 * If need - should be redefined in each controller where it required.
	 * @return array with permissions
	 * @example :
	 return array(
			S_METHODS => array(
				'save' => array(GMP_ADMIN),
				'remove' => array(GMP_ADMIN),
				'restore' => GMP_ADMIN,
			),
			S_USERLEVELS => array(
				S_ADMIN => array('save', 'remove', 'restore')
			),
		);
	 * Can be used on of sub-array - GMP_METHODS or GMP_USERLEVELS
	 */
	public function getPermissions() {
		return array();
	}
	public function getModule() {
		return frameGmp::_()->getModule( $this->getCode() );
	}
	protected function _prepareTextLikeSearch($val) {
		return '';	 // Should be re-defined for each type
	}
	protected function _prepareModelBeforeListSelect($model) {
		return $model;
	}
	/**
	 * Common method for list table data
	 */
	public function getListForTbl() {
		$res = new responseGmp();
		$res->ignoreShellData();
		$model = $this->getModel();
		
		$page = (int) reqGmp::getVar('page');
		$rowsLimit = (int) reqGmp::getVar('rows');
		$orderBy = reqGmp::getVar('sidx');
		$sortOrder = reqGmp::getVar('sord');

		// Our custom search
		$search = reqGmp::getVar('search');
		if($search && !empty($search) && is_array($search)) {
			foreach($search as $k => $v) {
				$v = trim($v);
				if(empty($v)) continue;
				if($k == 'text_like') {
					$v = $this->_prepareTextLikeSearch( $v );
					if(!empty($v)) {
						$model->addWhere(array('additionalCondition' => $v));
					}
				} else {
					$model->addWhere(array($k => $v));
				}
			}
		}
		// jqGrid search
		$isSearch = reqGmp::getVar('_search');
		if($isSearch) {
			$searchField = trim(reqGmp::getVar('searchField'));
			$searchString = trim(reqGmp::getVar('searchString'));
			if(!empty($searchField) && !empty($searchString)) {
				// For some cases - we will need to modify search keys and/or values before put it to the model
				$model->addWhere(array(
					$this->_prepareSearchField($searchField) => $this->_prepareSearchString($searchString)
				));
			}
		}
		$model = $this->_prepareModelBeforeListSelect($model);
		// Get total pages count for current request
		$totalCount = $model->getCount(array('clear' => array('selectFields')));
		$totalPages = 0;
		if($totalCount > 0) {
			$totalPages = ceil($totalCount / $rowsLimit);
		}
		if($page > $totalPages) {
			$page = $totalPages;
		}
		// Calc limits - to get data only for current set
		$limitStart = $rowsLimit * $page - $rowsLimit; // do not put $limit*($page - 1)
		if($limitStart < 0)
			$limitStart = 0;
		
 		$data = $model
			->setLimit($limitStart. ', '. $rowsLimit)
			->setOrderBy( $this->_prepareSortOrder($orderBy) )
			->setSortOrder( $sortOrder )
			->getFromTbl();
		
		$data = $this->_prepareListForTbl( $data );
		$res->addData('page', $page);
		$res->addData('total', $totalPages);
		$res->addData('rows', $data);
		$res->addData('records', $model->getLastGetCount());
		$res = dispatcherGmp::applyFilters($this->getCode(). '_getListForTblResults', $res);
		$res->ajaxExec();
	}
	public function removeGroup() {
		$res = new responseGmp();
		if($this->getModel()->removeGroup(reqGmp::getVar('listIds', 'post'))) {
			$res->addMessage(__('Done', GMP_LANG_CODE));
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function clear() {
		$res = new responseGmp();
		if($this->getModel()->clear()) {
			$res->addMessage(__('Done', GMP_LANG_CODE));
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	protected function _prepareListForTbl($data) {
		return $data;
	}
	protected function _prepareSearchField($searchField) {
		return $searchField;
	}
	protected function _prepareSearchString($searchString) {
		return $searchString;
	}
	protected function _prepareSortOrder($sortOrder) {
		return $sortOrder;
	}
}
