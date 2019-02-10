<?php
class iconsModelGmp extends modelGmp {
    public static $tableObj;
    function __construct() {
        if(empty(self::$tableObj)){
            self::$tableObj=  frameGmp::_()->getTable("icons");
        }
    }
	public function checkDefIcons() {
		if(!get_option('gmp_def_icons_installed') ){
			$this->setDefaultIcons();
		}
	}
	public function getIconsByIds($ids) {
		$icons = frameGmp::_()->getTable('icons')->get('*', array('additionalCondition' => 'id IN ('. implode(',', $ids). ')'));
        if(empty($icons) ){
			return $icons ;
        }
		if(!empty($icons)) {
			$iconsArr = array();
			foreach($icons as $i => $icon){
				$icon['path'] = $this->getIconUrl($icon['path']);
				$iconsArr[$icon['id']] = $icon;
			}
		}
        return $iconsArr;
	}
    public function getIcons($params = array()) {
		$fields = isset($params['fields']) ? $params['fields'] : '*';
        $icons = frameGmp::_()->getTable('icons')->get( $fields );
        if(empty($icons) ){
			return $icons ;
        }
        $iconsArr = array();
        foreach($icons as $icon){
            $icon['path'] = $this->getIconUrl($icon['path']);
            $iconsArr[$icon['id']] = $icon;
        }
        return $iconsArr;
    }
    public function saveNewIcon($params){
        if(!isset($params['url'])){
            $this->pushError(__("Icon no found", GMP_LANG_CODE));
            return false;
        }
        $url = $params['url'];
        $exists = self::$tableObj->get("*","`path`='".$url."'");
        if(!empty($exists)){
            return $exists[0]['id'];
        }
        return self::$tableObj->insert(array('path'=>$url,'title'=>$params['title'],
                                            'description'=>$params['description']));
        
    }
    public function getIconsPath(){
        return 'icons_files/def_icons/';
    }
    public function getIconsFullDir(){
        static $uplDir = '';
		if(empty($uplDir))
			$uplDir = wp_upload_dir();
        $modPath = $this->getModule()->getModPath();
        $path  = $modPath. $this->getIconsPath();
        return $path;
    }
    
    public function getIconsFullPath(){
        $uplDir = wp_upload_dir();
        $path = $uplDir['basedir']. $this->getIconsPath();
        return $path;
    }
    public function setDefaultIcons(){
		$jsonFile = frameGmp::_()->getModule('icons')->getModDir(). 'icons_files/icons.json';
		$icons = utilsGmp::jsonDecode(file_get_contents($jsonFile));
		
        $uplDir = wp_upload_dir();
        if(!is_dir($uplDir['basedir'])){
            @mkdir($uplDir['basedir'], 0777);
        }
        $icons_upload_path=$uplDir['basedir'].$this->getIconsPath();
        if(!is_dir($icons_upload_path)){
            @mkdir($icons_upload_path, 0777);
        }
        $qItems = array();
        foreach($icons as $icon){
           $qItems[] = "('".$icon['title']."','".$icon['description']."','".$icon['img']."')";               
       }
       $query = "insert into `@__icons` (`title`,`description`,`path`) VALUES ".implode(",",$qItems);       
       dbGmp::query($query);
       update_option('gmp_def_icons_installed', true);
    }
    public function downloadIconFromUrl($url){
        $filename = basename($url);
        if(empty($filename)){
            $this->pushError(__('File not found', GMP_LANG_CODE));
            return false;
        }
        $imageinfo = getimagesize ( $url,$imgProp );
        if(empty($imageinfo)){
            $this->pushError(__('Cannot get image', GMP_LANG_CODE));
            return false;
        }
        $fileExt = str_replace("image/","",$imageinfo['mime']);    
        $filename = utilsGmp::getRandStr(8).".".$fileExt;
        $dest = $this->getIconsFullPath().$filename;
        file_put_contents($dest, fopen($url, 'r')); 
        $newIconId = frameGmp::_()->getTable('icons')->store(array('path'=>$filename),"insert");
        if($newIconId){
           return array('id'=>$newIconId,'path'=>$this->getIconsFullDir().$filename);            
        }else{
            $this->pushError(__('cannot insert to table', GMP_LANG_CODE));
            return false;
        }
    }
	
	public function getIconFromId($id){
		$res = frameGmp::_()->getTable('icons')->get("*", array('id'=>$id));
		if(empty($res)){
			return $res;
		}
		$icon =$res[0]; 
		$icon['path'] = $this->getIconUrl($icon['path']);
		return $icon;
	}
	function getIconUrl($icon){
		if(!empty($icon)){
			$isUrl = strpos($icon, 'http');
			if($isUrl === false){
				$icon = $this->getIconsFullDir(). $icon;             
			}
			if(uriGmp::isHttps()) {
				$icon = uriGmp::makeHttps($icon);
			}
		}
		return $icon;
	}
	public function iconExists($iconId) {
		return self::$tableObj->exists($iconId, 'id');
	}
	public function remove($d = array()) {
		$d['id'] = isset($d['id']) ? (int) $d['id'] : 0;
		if($d['id']) {
			if(frameGmp::_()->getTable('icons')->delete(array('id' => $d['id']))) {
				$this->replaceDeletedIconIdToDefault($d['id']);
				return true;
			} else
				$this->pushError (frameGmp::_()->getTable('icons')->getErrors());
		} else
			$this->pushError (__('Invalid ID', GMP_LANG_CODE));
		return false;
	}
	public function replaceDeletedIconIdToDefault($idIcon){
		if(frameGmp::_()->getModule('marker')->getModel()->replaceDeletedIconIdToDefault($idIcon)) {
			return true;
		} else {
			$this->pushError (frameGmp::_()->getTable('icons')->getErrors());
		}
		return false;
	}

}