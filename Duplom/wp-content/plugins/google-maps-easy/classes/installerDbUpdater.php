<?php
class installerDbUpdaterGmp {
	static public function runUpdate() {
		self::update_105();
		self::update_109();
		self::update_117();
		self::update_192();
	}
	public static function update_105() {
		if(!dbGmp::exist('@__modules', 'code', 'csv')) {
			dbGmp::query("INSERT INTO `@__modules` (id, code, active, type_id, params, has_tab, label, description)
				VALUES (NULL, 'csv', 1, 1, '', 0, 'csv', 'csv')");
		}
	}
	public static function update_109() {
		if(!dbGmp::exist('@__modules', 'code', 'gmap_widget')) {
			dbGmp::query("INSERT INTO `@__modules` (id, code, active, type_id, params, has_tab, label, description)
				VALUES (NULL, 'gmap_widget', 1, 1, '', 0, 'gmap_widget', 'gmap_widget')");
		}
	}
	public static function update_117() {
		dbGmp::query("UPDATE @__options SET value_type = 'array' WHERE code = 'infowindow_size' LIMIT 1");
	}

	public static function update_192() {
		global $wpdb;
		$wpPrefix = $wpdb->prefix;

		if(!dbGmp::exist($wpPrefix.GMP_DB_PREF."markers", 'period_from')) {
			dbGmp::query("ALTER TABLE `@__markers` ADD COLUMN `period_from` DATE NULL;");
		}
		if(!dbGmp::exist($wpPrefix.GMP_DB_PREF."markers", 'period_to')) {
			dbGmp::query("ALTER TABLE `@__markers` ADD COLUMN `period_to` DATE NULL;");
		}
		if(!dbGmp::exist($wpPrefix.GMP_DB_PREF."markers", 'hash')) {
			dbGmp::query("ALTER TABLE `@__markers` ADD COLUMN `hash` varchar(32) DEFAULT NULL;");
		}
	}
}