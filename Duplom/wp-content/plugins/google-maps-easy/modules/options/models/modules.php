<?php
class modulesModelGmp extends modelGmp {
	public function __construct() {
		$this->_setTbl('modules');
	}
    public function get($d = array()) {
        if(isset($d['id']) && $d['id'] && is_numeric($d['id'])) {
            $fields = frameGmp::_()->getTable('modules')->fillFromDB($d['id'])->getFields();
            $fields['types'] = array();
            $types = frameGmp::_()->getTable('modules_type')->fillFromDB();
            foreach($types as $t) {
                $fields['types'][$t['id']->value] = $t['label']->value;
            }
            return $fields;
        } elseif(!empty($d)) {
            $data = frameGmp::_()->getTable('modules')->get('*', $d);
            return $data;
        } else {
            return frameGmp::_()->getTable('modules')
                ->innerJoin(frameGmp::_()->getTable('modules_type'), 'type_id')
                ->getAll(frameGmp::_()->getTable('modules')->alias().'.*, '. frameGmp::_()->getTable('modules_type')->alias(). '.label as type');
        }
    }
    public function put($d = array()) {
        $res = new responseGmp();
        $id = $this->_getIDFromReq($d);
        $d = prepareParamsGmp($d);
        if(is_numeric($id) && $id) {
            if(isset($d['active']))
                $d['active'] = ((is_string($d['active']) && $d['active'] == 'true') || $d['active'] == 1) ? 1 : 0;           //mmm.... govnokod?....)))
           /* else
                 $d['active'] = 0;*/
            
            if(frameGmp::_()->getTable('modules')->update($d, array('id' => $id))) {
                $res->messages[] = __('Module Updated', GMP_LANG_CODE);
                $mod = frameGmp::_()->getTable('modules')->getById($id);
                $newType = frameGmp::_()->getTable('modules_type')->getById($mod['type_id'], 'label');
                $newType = $newType['label'];
                $res->data = array(
                    'id' => $id, 
                    'label' => $mod['label'], 
                    'code' => $mod['code'], 
                    'type' => $newType,
                    'active' => $mod['active'], 
                );
            } else {
                if($tableErrors = frameGmp::_()->getTable('modules')->getErrors()) {
                    $res->errors = array_merge($res->errors, $tableErrors);
                } else
                    $res->errors[] = __('Module Update Failed', GMP_LANG_CODE);
            }
        } else {
            $res->errors[] = __('Error module ID', GMP_LANG_CODE);
        }
        return $res;
    }
    protected function _getIDFromReq($d = array()) {
        $id = 0;
        if(isset($d['id']))
            $id = $d['id'];
        elseif(isset($d['code'])) {
            $fromDB = $this->get(array('code' => $d['code']));
            if(!empty($fromDB[0]) && $fromDB[0]['id'])
                $id = $fromDB[0]['id'];
        }
        return $id;
    }
}
