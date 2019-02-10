<?php
    global $wpdb;
    if (!defined('WPLANG') || WPLANG == '') {
        define('GMP_WPLANG', 'en_GB');
    } else {
        define('GMP_WPLANG', WPLANG);
    }
    if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

    define('GMP_PLUG_NAME', basename(dirname(__FILE__)));
    define('GMP_DIR', WP_PLUGIN_DIR. DS. GMP_PLUG_NAME. DS);
    define('GMP_TPL_DIR', GMP_DIR. 'tpl'. DS);
    define('GMP_CLASSES_DIR', GMP_DIR. 'classes'. DS);
    define('GMP_TABLES_DIR', GMP_CLASSES_DIR. 'tables'. DS);
	define('GMP_HELPERS_DIR', GMP_CLASSES_DIR. 'helpers'. DS);
    define('GMP_LANG_DIR', GMP_DIR. 'lang'. DS);
    define('GMP_IMG_DIR', GMP_DIR. 'img'. DS);
    define('GMP_TEMPLATES_DIR', GMP_DIR. 'templates'. DS);
    define('GMP_MODULES_DIR', GMP_DIR. 'modules'. DS);
    define('GMP_FILES_DIR', GMP_DIR. 'files'. DS);
    define('GMP_ADMIN_DIR', ABSPATH. 'wp-admin'. DS);

	define('GMP_PLUGINS_URL', plugins_url());
    define('GMP_SITE_URL', get_bloginfo('wpurl'). '/');
    define('GMP_JS_PATH', GMP_PLUGINS_URL. '/'. GMP_PLUG_NAME. '/js/');
    define('GMP_CSS_PATH', GMP_PLUGINS_URL. '/'. GMP_PLUG_NAME. '/css/');
    define('GMP_IMG_PATH', GMP_PLUGINS_URL. '/'. GMP_PLUG_NAME. '/img/');
    define('GMP_MODULES_PATH', GMP_PLUGINS_URL. '/'. GMP_PLUG_NAME. '/modules/');
    define('GMP_TEMPLATES_PATH', GMP_PLUGINS_URL. '/'. GMP_PLUG_NAME. '/templates/');
    define('GMP_JS_DIR', GMP_DIR. 'js/');

    define('GMP_URL', GMP_SITE_URL);

    define('GMP_LOADER_IMG', GMP_IMG_PATH. 'loading.gif');
	define('GMP_TIME_FORMAT', 'H:i:s');
    define('GMP_DATE_DL', '/');
    define('GMP_DATE_FORMAT', 'm/d/Y');
    define('GMP_DATE_FORMAT_HIS', 'm/d/Y ('. GMP_TIME_FORMAT. ')');
    define('GMP_DATE_FORMAT_JS', 'mm/dd/yy');
    define('GMP_DATE_FORMAT_CONVERT', '%m/%d/%Y');
    define('GMP_WPDB_PREF', $wpdb->prefix);
    define('GMP_DB_PREF', 'gmp_');
    define('GMP_MAIN_FILE', 'gmp.php');

    define('GMP_DEFAULT', 'default');
    define('GMP_CURRENT', 'current');
	
	define('GMP_EOL', "\n");    
    
    define('GMP_PLUGIN_INSTALLED', true);
    define('GMP_VERSION_PLUGIN', '1.9.15');	//GMP_VERSION is pre-defined constant for PHP GMP module http://php.net/manual/en/book.gmp.php
    define('GMP_USER', 'user');
    
    define('GMP_CLASS_PREFIX', 'gmpc');     
    define('GMP_FREE_VERSION', false);
	define('GMP_TEST_MODE', true);
    
    define('GMP_SUCCESS', 'Success');
    define('GMP_FAILED', 'Failed');
	define('GMP_ERRORS', 'gmpErrors');
	
	define('GMP_ADMIN',	'admin');
	define('GMP_LOGGED','logged');
	define('GMP_GUEST',	'guest');
	
	define('GMP_ALL',		'all');
	
	define('GMP_METHODS',		'methods');
	define('GMP_USERLEVELS',	'userlevels');
	/**
	 * Framework instance code, unused for now
	 */
	define('GMP_CODE', 'gmp');

	define('GMP_LANG_CODE', 'gmp_lng');
	/**
	 * Plugin name
	 */
	define('GMP_WP_PLUGIN_NAME', 'Google Maps Easy');
	/**
	 * Custom defined for plugin
	 */
	define('GMP_COMMON', 'common');
	define('GMP_FB_LIKE', 'fb_like');
	define('GMP_VIDEO', 'video');
	
	define('GMP_SHORTCODE', 'google_map_easy');
