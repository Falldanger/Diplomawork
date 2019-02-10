<?php
class modInstallerGmp {
    static private $_current = array();
    /**
     * Install new moduleGmp into plugin
     * @param string $module new moduleGmp data (@see classes/tables/modules.php)
     * @param string $path path to the main plugin file from what module is installed
     * @return bool true - if install success, else - false
     */
    static public function install($module, $path) {
        $exPlugDest = explode('plugins', $path);
        if(!empty($exPlugDest[1])) {
            $module['ex_plug_dir'] = str_replace(DS, '', $exPlugDest[1]);
        }
        $path = $path. DS. $module['code'];
        if(!empty($module) && !empty($path) && is_dir($path)) {
            if(self::isModule($path)) {
                $filesMoved = false;
                if(empty($module['ex_plug_dir']))
                    $filesMoved = self::moveFiles($module['code'], $path);
                else
                    $filesMoved = true;     //Those modules doesn't need to move their files
                if($filesMoved) {
                    if(frameGmp::_()->getTable('modules')->exists($module['code'], 'code')) {
                        frameGmp::_()->getTable('modules')->delete(array('code' => $module['code']));
                    }
					if($module['code'] != 'license')
						$module['active'] = 0;
                    frameGmp::_()->getTable('modules')->insert($module);
                    self::_runModuleInstall($module);
                    self::_installTables($module);
                    return true;
                } else {
                    errorsGmp::push(sprintf(__('Move files for %s failed'), $module['code']), errorsGmp::MOD_INSTALL);
                }
            } else
                errorsGmp::push(sprintf(__('%s is not plugin module'), $module['code']), errorsGmp::MOD_INSTALL);
        }
        return false;
    }
    static protected function _runModuleInstall($module, $action = 'install') {
        $moduleLocationDir = GMP_MODULES_DIR;
        if(!empty($module['ex_plug_dir']))
            $moduleLocationDir = utilsGmp::getPluginDir( $module['ex_plug_dir'] );
        if(is_dir($moduleLocationDir. $module['code'])) {
			if(!class_exists($module['code']. strFirstUp(GMP_CODE))) {
				importClassGmp($module['code'], $moduleLocationDir. $module['code']. DS. 'mod.php');
			}
            $moduleClass = toeGetClassNameGmp($module['code']);
            $moduleObj = new $moduleClass($module);
            if($moduleObj) {
                $moduleObj->$action();
            }
        }
    }
    /**
     * Check whether is or no module in given path
     * @param string $path path to the module
     * @return bool true if it is module, else - false
     */
    static public function isModule($path) {
        return true;
    }
    /**
     * Move files to plugin modules directory
     * @param string $code code for module
     * @param string $path path from what module will be moved
     * @return bool is success - true, else - false
     */
    static public function moveFiles($code, $path) {
        if(!is_dir(GMP_MODULES_DIR. $code)) {
            if(mkdir(GMP_MODULES_DIR. $code)) {
                utilsGmp::copyDirectories($path, GMP_MODULES_DIR. $code);
                return true;
            } else 
                errorsGmp::push(__('Can not create module directory. Try to set permission to '. GMP_MODULES_DIR. ' directory 755 or 777', GMP_LANG_CODE), errorsGmp::MOD_INSTALL);
        } else
            return true;
        return false;
    }
    static private function _getPluginLocations() {
        $locations = array();
        $plug = reqGmp::getVar('plugin');
        if(empty($plug)) {
            $plug = reqGmp::getVar('checked');
            $plug = $plug[0];
        }
        $locations['plugPath'] = plugin_basename( trim( $plug ) );
        $locations['plugDir'] = dirname(WP_PLUGIN_DIR. DS. $locations['plugPath']);
		$locations['plugMainFile'] = WP_PLUGIN_DIR. DS. $locations['plugPath'];
        $locations['xmlPath'] = $locations['plugDir']. DS. 'install.xml';
        return $locations;
    }
    static private function _getModulesFromXml($xmlPath) {
        if($xml = utilsGmp::getXml($xmlPath)) {
            if(isset($xml->modules) && isset($xml->modules->mod)) {
                $modules = array();
                $xmlMods = $xml->modules->children();
                foreach($xmlMods->mod as $mod) {
                    $modules[] = $mod;
                }
                if(empty($modules))
                    errorsGmp::push(__('No modules were found in XML file', GMP_LANG_CODE), errorsGmp::MOD_INSTALL);
                else
                    return $modules;
            } else
                errorsGmp::push(__('Invalid XML file', GMP_LANG_CODE), errorsGmp::MOD_INSTALL);
        } else
            errorsGmp::push(__('No XML file were found', GMP_LANG_CODE), errorsGmp::MOD_INSTALL);
        return false;
    }
    /**
     * Check whether modules is installed or not, if not and must be activated - install it
     * @param array $codes array with modules data to store in database
     * @param string $path path to plugin file where modules is stored (__FILE__ for example)
     * @return bool true if check ok, else - false
     */
    static public function check($extPlugName = '') {
        $locations = self::_getPluginLocations();
        if($modules = self::_getModulesFromXml($locations['xmlPath'])) {
            foreach($modules as $m) {
                $modDataArr = utilsGmp::xmlNodeAttrsToArr($m);
                if(!empty($modDataArr)) {
                    if(frameGmp::_()->moduleExists($modDataArr['code'])) { //If module Exists - just activate it
                        self::activate($modDataArr);
                    } else {                                           //  if not - install it
                        if(!self::install($modDataArr, $locations['plugDir'])) {
                            errorsGmp::push(sprintf(__('Install %s failed'), $modDataArr['code']), errorsGmp::MOD_INSTALL);
                        }
                    }
                }
            }
        } else
            errorsGmp::push(__('Error Activate module', GMP_LANG_CODE), errorsGmp::MOD_INSTALL);
        if(errorsGmp::haveErrors(errorsGmp::MOD_INSTALL)) {
            self::displayErrors();
            return false;
        }
		update_option(GMP_CODE. '_full_installed', 1);
        return true;
    }
    /**
	 * Public alias for _getCheckRegPlugs()
	 */
	/**
	 * We will run this each time plugin start to check modules activation messages
	 */
	static public function checkActivationMessages() {

	}
    /**
     * Deactivate module after deactivating external plugin
     */
    static public function deactivate() {
        $locations = self::_getPluginLocations();
        if($modules = self::_getModulesFromXml($locations['xmlPath'])) {
            foreach($modules as $m) {
                $modDataArr = utilsGmp::xmlNodeAttrsToArr($m);
                if(frameGmp::_()->moduleActive($modDataArr['code'])) { //If module is active - then deacivate it
                    if(frameGmp::_()->getModule('options')->getModel('modules')->put(array(
                        'id' => frameGmp::_()->getModule($modDataArr['code'])->getID(),
                        'active' => 0,
                    ))->error) {
                        errorsGmp::push(__('Error Deactivation module', GMP_LANG_CODE), errorsGmp::MOD_INSTALL);
                    }
                }
            }
        }
        if(errorsGmp::haveErrors(errorsGmp::MOD_INSTALL)) {
            self::displayErrors(false);
            return false;
        }
        return true;
    }
    static public function activate($modDataArr) {
        $locations = self::_getPluginLocations();
        if($modules = self::_getModulesFromXml($locations['xmlPath'])) {
            foreach($modules as $m) {
                $modDataArr = utilsGmp::xmlNodeAttrsToArr($m);
                if(!frameGmp::_()->moduleActive($modDataArr['code'])) { //If module is not active - then acivate it
                    if(frameGmp::_()->getModule('options')->getModel('modules')->put(array(
                        'code' => $modDataArr['code'],
                        'active' => 1,
                    ))->error) {
                        errorsGmp::push(__('Error Activating module', GMP_LANG_CODE), errorsGmp::MOD_INSTALL);
                    } else {
						$dbModData = frameGmp::_()->getModule('options')->getModel('modules')->get(array('code' => $modDataArr['code']));
						if(!empty($dbModData) && !empty($dbModData[0])) {
							$modDataArr['ex_plug_dir'] = $dbModData[0]['ex_plug_dir'];
						}
						self::_runModuleInstall($modDataArr, 'activate');
					}
                }
            }
        }
    } 
    /**
     * Display all errors for module installer, must be used ONLY if You realy need it
     */
    static public function displayErrors($exit = true) {
        $errors = errorsGmp::get(errorsGmp::MOD_INSTALL);
        foreach($errors as $e) {
            echo '<b style="color: red;">'. $e. '</b><br />';
        }
        if($exit) exit();
    }
    static public function uninstall() {
        $locations = self::_getPluginLocations();
        if($modules = self::_getModulesFromXml($locations['xmlPath'])) {
            foreach($modules as $m) {
                $modDataArr = utilsGmp::xmlNodeAttrsToArr($m);
                self::_uninstallTables($modDataArr);
                frameGmp::_()->getModule('options')->getModel('modules')->delete(array('code' => $modDataArr['code']));
                utilsGmp::deleteDir(GMP_MODULES_DIR. $modDataArr['code']);
            }
        }
    }
    static protected  function _uninstallTables($module) {
        if(is_dir(GMP_MODULES_DIR. $module['code']. DS. 'tables')) {
            $tableFiles = utilsGmp::getFilesList(GMP_MODULES_DIR. $module['code']. DS. 'tables');
            if(!empty($tableNames)) {
                foreach($tableFiles as $file) {
                    $tableName = str_replace('.php', '', $file);
                    if(frameGmp::_()->getTable($tableName))
                        frameGmp::_()->getTable($tableName)->uninstall();
                }
            }
        }
    }
    static public function _installTables($module, $action = 'install') {
		$modDir = empty($module['ex_plug_dir']) ? 
            GMP_MODULES_DIR. $module['code']. DS : 
            utilsGmp::getPluginDir($module['ex_plug_dir']). $module['code']. DS; 
        if(is_dir($modDir. 'tables')) {
            $tableFiles = utilsGmp::getFilesList($modDir. 'tables');
            if(!empty($tableFiles)) {
                frameGmp::_()->extractTables($modDir. 'tables'. DS);
                foreach($tableFiles as $file) {
                    $tableName = str_replace('.php', '', $file);
                    if(frameGmp::_()->getTable($tableName))
                        frameGmp::_()->getTable($tableName)->$action();
                }
            }
        }
    }
}