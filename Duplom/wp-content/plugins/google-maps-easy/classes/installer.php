<?php
class installerGmp {
	static public $update_to_version_method = '';
	static public function init() {
		global $wpdb;
		$wpPrefix = $wpdb->prefix; /* add to 0.0.3 Versiom */
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$current_version = get_option($wpPrefix. GMP_DB_PREF. 'db_version', 0);
		$installed = (int) get_option($wpPrefix. GMP_DB_PREF. 'db_installed', 0);
		/**
		 * modules 
		 */
		if (!dbGmp::exist($wpPrefix.GMP_DB_PREF."modules")) {
			dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.GMP_DB_PREF."modules` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `code` varchar(64) NOT NULL,
			  `active` tinyint(1) NOT NULL DEFAULT '0',
			  `type_id` smallint(3) NOT NULL DEFAULT '0',
			  `params` text,
			  `has_tab` tinyint(1) NOT NULL DEFAULT '0',
			  `label` varchar(128) DEFAULT NULL,
			  `description` text,
			  `ex_plug_dir` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE INDEX `code` (`code`)
			) DEFAULT CHARSET=utf8;");

			dbGmp::query("INSERT INTO `".$wpPrefix.GMP_DB_PREF."modules` (id, code, active, type_id, params, has_tab, label, description) VALUES
				(NULL, 'adminmenu',1,1,'',0,'Admin Menu',''),
				(NULL, 'options',1,1,'',1,'Options',''),
				(NULL, 'user',1,1,'',1,'Users',''),
				(NULL, 'templates',1,1,'',1,'Templates for Plugin',''),

				(NULL, 'shortcodes', 1, 6, '', 0, 'Shortcodes', 'Shortcodes data'),
				(NULL, 'gmap', 1, 1, '',1, 'Gmap', 'Gmap'),
				(NULL, 'marker', 1, 1, '', 0, 'Markers', 'Google Maps Markers'),
				(NULL, 'marker_groups', 1, 1, '', 0, 'Marker Gropus', 'Marker Groups'),                  
				(NULL, 'supsystic_promo', 1, 1, '', 0, 'Promo', 'Promo'),
				(NULL, 'icons', 1, 1, '', 1, 'Marker Icons', 'Marker Icons'),
				(NULL, 'mail', 1, 1, '', 1, 'mail', 'mail');");
		}
		if(dbGmp::exist('@__modules', 'code', 'promo')) {
			dbGmp::query('UPDATE @__modules SET code = "supsystic_promo" WHERE code = "promo"');
		}
		if(!dbGmp::exist('@__modules', 'code', 'mail')) {
			dbGmp::query("INSERT INTO `".$wpPrefix.GMP_DB_PREF."modules` (id, code, active, type_id, params, has_tab, label, description) VALUES
				(NULL, 'mail', 1, 1, '', 1, 'mail', 'mail');");
		}
		if(!dbGmp::exist('@__modules', 'code', 'supsystic_promo')) {
			dbGmp::query("INSERT INTO `@__modules` (id, code, active, type_id, params, has_tab, label, description) VALUES
				(NULL, 'supsystic_promo', 1, 1, '', 1, 'supsystic_promo', 'supsystic_promo');");
		}
		/**
		 *  modules_type 
		 */
		if(!dbGmp::exist($wpPrefix.GMP_DB_PREF."modules_type")) {
			dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.GMP_DB_PREF."modules_type` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `label` varchar(64) NOT NULL,
			  PRIMARY KEY (`id`)
			) AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;");
		
			dbGmp::query("INSERT INTO `".$wpPrefix.GMP_DB_PREF."modules_type` VALUES
			  (1,'system'),
			  (4,'widget'),
			  (6,'addons'),
			  (7,'template')");
		}
		/**
		 * options 
		 */
		if(!dbGmp::exist($wpPrefix.GMP_DB_PREF."options")) {
			dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.GMP_DB_PREF."options` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `code` varchar(64) CHARACTER SET latin1 NOT NULL,
			  `value` text NULL,
			  `label` varchar(128) CHARACTER SET latin1 DEFAULT NULL,
			  `description` text CHARACTER SET latin1,
			  `htmltype_id` smallint(2) NOT NULL DEFAULT '1',
			  `params` text NULL,
			  `cat_id` mediumint(3) DEFAULT '0',
			  `sort_order` mediumint(3) DEFAULT '0',
			  `value_type` varchar(16) CHARACTER SET latin1 DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `id` (`id`),
			  UNIQUE INDEX `code` (`code`)
			) DEFAULT CHARSET=utf8");
			dbGmp::query("insert into `".$wpPrefix.GMP_DB_PREF."options` (  `code` ,  `value` ,  `label` ) 
					VALUES ( 'save_statistic',  '0',  'Send statistic')");
			dbGmp::query("insert into `@__options` (`code`,`value`,`label`) VALUES
			('infowindow_size','". utilsGmp::serialize(array('width'=>'100','height'=>'100')). "','Info Window Size')");
		}
		/* options categories */
		if(!dbGmp::exist($wpPrefix.GMP_DB_PREF."options_categories")) {
			dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.GMP_DB_PREF."options_categories` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `label` varchar(128) NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `id` (`id`)
			) DEFAULT CHARSET=utf8");
			dbGmp::query("INSERT INTO `".$wpPrefix.GMP_DB_PREF."options_categories` VALUES
				(1, 'General'),
				(2, 'Template'),
				(3, 'Subscribe'),
				(4, 'Social');");
		}
		/*
		* Create table for map
		*/
        if(!dbGmp::exist($wpPrefix.GMP_DB_PREF."maps")) {
			dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.GMP_DB_PREF."maps` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`title` varchar(125) CHARACTER SET utf8  NOT NULL,
				`description` text CHARACTER SET utf8 NULL,
				`params` text NULL,
				`html_options` text NOT NULL,
				`create_date` datetime,
				PRIMARY KEY (`id`),
				UNIQUE INDEX `id` (`id`)
			  ) DEFAULT CHARSET=utf8");
		}
		/**
		 * Create table for markers    
		 */
		if(!dbGmp::exist($wpPrefix.GMP_DB_PREF."markers")){
			dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.GMP_DB_PREF."markers"."` (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`title` varchar(125) CHARACTER SET utf8 NOT NULL,
					`description` text CHARACTER SET utf8 NULL,
					`coord_x` varchar(30) CHARACTER SET utf8 NOT NULL,
					`coord_y` varchar(30) CHARACTER SET utf8 NOT NULL,
					`icon` int(11),
					`map_id` int(11),
					`marker_group_id` int(11),
					`address` text CHARACTER SET utf8,
					`animation` int(1),
					`create_date` datetime,
					`params` text  CHARACTER SET utf8 NOT NULL,
					`sort_order` tinyint(1) NOT NULL DEFAULT '0',
					`user_id` int(11),
					PRIMARY KEY (`id`)
				) DEFAULT CHARSET=utf8");
		}
		if(!dbGmp::exist($wpPrefix.GMP_DB_PREF."markers", 'sort_order')) {
			dbGmp::query("ALTER TABLE `@__markers` ADD COLUMN `sort_order` tinyint(1) NOT NULL DEFAULT '0';");
		}
		if(!dbGmp::exist($wpPrefix.GMP_DB_PREF."markers", 'user_id')) {
			dbGmp::query("ALTER TABLE `@__markers` ADD COLUMN `user_id` int(11);");
		}
		/**
		 * Create table for marker Icons
		 */
		if(!dbGmp::exist($wpPrefix.GMP_DB_PREF."icons")){
			dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.GMP_DB_PREF."icons"."` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`title` varchar(100) CHARACTER SET utf8,   
				`description` text CHARACTER SET utf8,   
				`path` varchar(250) CHARACTER SET utf8,   
				 PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8");
		}

		/**
		 * Create table for marker groups
		 */
		if(!dbGmp::exist($wpPrefix.GMP_DB_PREF."marker_groups")){
			dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.GMP_DB_PREF."marker_groups"."` (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`title` varchar(250) CHARACTER SET utf8,
					`description` text CHARACTER SET utf8,
					`params` text CHARACTER SET utf8,
					`sort_order` tinyint(1) NOT NULL DEFAULT '0',
				 PRIMARY KEY (`id`)
				  ) DEFAULT CHARSET=utf8");
		}
		if(!dbGmp::exist($wpPrefix.GMP_DB_PREF."marker_groups", 'params')) {
			dbGmp::query("ALTER TABLE @__marker_groups ADD params text CHARACTER SET utf8;");
		}
		if(!dbGmp::exist($wpPrefix.GMP_DB_PREF."marker_groups", 'sort_order')) {
			dbGmp::query("ALTER TABLE @__marker_groups ADD sort_order tinyint(1) NOT NULL DEFAULT '0';");
		}
		if(!dbGmp::exist($wpPrefix.GMP_DB_PREF."marker_groups", 'parent')) {
			dbGmp::query("ALTER TABLE @__marker_groups ADD parent tinyint(1) NOT NULL DEFAULT '0' AFTER params;");
		}
		$markerGroupsClearedInvalid = get_option($wpPrefix. GMP_DB_PREF. 'mg_cleared_inv', 0);
		if(!$markerGroupsClearedInvalid) {
			dbGmp::query('UPDATE @__markers SET marker_group_id = 0 WHERE marker_group_id = 1');	// This was wrong update in markers table before - fix this one time before update plugin
			update_option($wpPrefix. GMP_DB_PREF. 'mg_cleared_inv', 1);
		}
		/**
		* Plugin usage statistics
		*/
		if(!dbGmp::exist($wpPrefix.GMP_DB_PREF."usage_stat")) {
			dbDelta("CREATE TABLE `".$wpPrefix.GMP_DB_PREF."usage_stat` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `code` varchar(64) NOT NULL,
			  `visits` int(11) NOT NULL DEFAULT '0',
			  `spent_time` int(11) NOT NULL DEFAULT '0',
			  `modify_timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			  UNIQUE INDEX `code` (`code`),
			  PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8");
			dbGmp::query("INSERT INTO `".$wpPrefix.GMP_DB_PREF."usage_stat` (code, visits) VALUES ('installed', 1)");
		}
		/**
		 * Membership integration
		 */
		if(!dbGmp::exist($wpPrefix.GMP_DB_PREF.'membership_presets')) {
			dbDelta("CREATE TABLE `".$wpPrefix.GMP_DB_PREF."membership_presets` (
				`maps_id` int(11) NOT NULL,
				`allow_use` TINYINT(1) NOT NULL DEFAULT 0,
				PRIMARY KEY (`maps_id`)
			)  DEFAULT CHARSET=utf8");
		}
		if(!dbGmp::exist('@__modules', 'code', 'membership')) {
			dbGmp::query("INSERT INTO `@__modules` (id, code, active, type_id, params, has_tab, label, description) VALUES
				(NULL, 'membership', 1, 1, '', 1, 'membership', 'membership');");
		}
		/**
		 * Create table for marker groups
		 */
		if(!dbGmp::exist($wpPrefix.GMP_DB_PREF."marker_groups_relation")){
			dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.GMP_DB_PREF."marker_groups_relation"."` (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`marker_id` int(11) NOT NULL,
					`groups_id` int(11) NOT NULL,
				 PRIMARY KEY (`id`)
				  ) DEFAULT CHARSET=utf8");
		}

		if( !get_option($wpPrefix. GMP_DB_PREF. 'markers_groups_multiple_updated') ){
			$markersData = dbGmp::get("SELECT `id`,`marker_group_id` FROM @__markers", 'all', ARRAY_A);
			if($markersData){
				foreach ($markersData as $marker) {
					dbGmp::query("INSERT INTO `".$wpPrefix.GMP_DB_PREF."marker_groups_relation` (marker_id, groups_id) VALUES
					(".$marker['id'].", ".$marker['marker_group_id'].");");
				}
				update_option($wpPrefix. GMP_DB_PREF. 'markers_groups_multiple_updated', 1);
			}
		}
        update_option($wpPrefix. GMP_DB_PREF. 'db_version', GMP_VERSION_PLUGIN);
		add_option($wpPrefix. GMP_DB_PREF. 'db_installed', 1);
		
        installerDbUpdaterGmp::runUpdate();
	}
	static public function setUsed() {
		update_option(GMP_DB_PREF. 'plug_was_used', 1);
	}
	static public function isUsed() {
		// No welcome page for now
		return true;
		return (bool)get_option(GMP_DB_PREF. 'plug_was_used');
	}
	/**
	 * Create pages for plugin usage
	 */
	static public function createPages() {
		return false;
	}
	
	/**
	 * Return page data from given array, searched by title, used in self::createPages()
	 * @return mixed page data object if success, else - false
	 */
	static private function _getPageByTitle($title, $pageArr) {
		foreach($pageArr as $p) {
			if($p->title == $title)
				return $p;
		}
		return false;
	}
	static public function delete() {
		self::_checkSendStat('delete');
		global $wpdb;
		$wpPrefix = $wpdb->prefix; /* add to 0.0.3 Versiom */
		$deleteOptions = false;
		if((bool)$deleteOptions){
		   $wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.GMP_DB_PREF."modules`");
		   $wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.GMP_DB_PREF."icons`");
		   $wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.GMP_DB_PREF."maps`");
		   $wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.GMP_DB_PREF."options`");
		   $wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.GMP_DB_PREF."htmltype`");
		   $wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.GMP_DB_PREF."markers`");
		   $wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.GMP_DB_PREF."marker_groups`");
		   $wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.GMP_DB_PREF."options_categories`");
		   $wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.GMP_DB_PREF."modules_type`");
		   $wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.GMP_DB_PREF."usage_stat`");
		   $wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.GMP_DB_PREF."membership_presets`");

		   delete_option('gmp_def_icons_installed');
		   delete_option(GMP_DB_PREF. 'db_version');
		   delete_option($wpPrefix.GMP_DB_PREF.'db_installed');
		   //delete_option(GMP_DB_PREF. 'plug_was_used');       
		}
	}
	static public function deactivate() {
		self::_checkSendStat('deactivate');
	}
	static private function _checkSendStat($statCode) {
		if(class_exists('frameGmp') 
			&& frameGmp::_()->getModule('supsystic_promo')
			&& frameGmp::_()->getModule('options')
		) {
			frameGmp::_()->getModule('supsystic_promo')->getModel()->saveUsageStat( $statCode );
			frameGmp::_()->getModule('supsystic_promo')->getModel()->checkAndSend( true );
		}
	}
	static public function update() {
		global $wpdb;
		$wpPrefix = $wpdb->prefix; /* add to 0.0.3 Versiom */
		$currentVersion = get_option($wpPrefix. GMP_DB_PREF. 'db_version', 0);
		$installed = (int) get_option($wpPrefix. GMP_DB_PREF. 'db_installed', 0);
		if(!$currentVersion || version_compare(GMP_VERSION_PLUGIN, $currentVersion, '>')) {
			self::init();
			update_option($wpPrefix. GMP_DB_PREF. 'db_version', GMP_VERSION_PLUGIN);
		}
	}
}
