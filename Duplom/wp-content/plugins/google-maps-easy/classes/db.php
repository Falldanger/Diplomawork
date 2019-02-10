<?php
/**
 * Shell - class to work with $wpdb global object
 */
class dbGmp {
    /**
     * Execute query and return results
     * @param string $query query to be executed
     * @param string $get what must be returned - one value (one), one row (row), one col (col) or all results (all - by default)
     * @param const $outputType type of returned data
     * @return mixed data from DB
     */
    static public $query = '';
    static public function get($query, $get = 'all', $outputType = ARRAY_A) {
        global $wpdb;
        $get = strtolower($get);
        $res = NULL;
        $query = self::prepareQuery($query);
        self::$query = $query;
        switch($get) {
            case 'one':
                $res = $wpdb->get_var($query);
                break;
            case 'row':
                $res = $wpdb->get_row($query, $outputType);
                break;
            case 'col':
                $res = $wpdb->get_col($query);
                break;
            case 'all':
            default:
                $res = $wpdb->get_results($query, $outputType);
                break;
        }
        return $res;
    }
    /**
     * Execute one query
     * @return query results
     */
    static public function query($query) {
        global $wpdb;
        return ($wpdb->query( self::prepareQuery($query) ) === false ? false : true);
    }
    /**
     * Get last insert ID
     * @return int last ID
     */
    static public function insertID() {
        global $wpdb;
        return $wpdb->insert_id;
    }
    /**
     * Get number of rows returned by last query
     * @return int number of rows
     */
    static public function numRows() {
        global $wpdb;
        return $wpdb->num_rows;
    }
    /**
     * Replace prefixes in custom query. Suported next prefixes:
     * #__  Worgmpess prefix
     * ^__  Store plugin tables prefix (@see GMP_DB_PREF if config.php)
     * @__  Compared of WP table prefix + Store plugin prefix (@example wp_s_)
     * @param string $query query to be executed
     */
    static public function prepareQuery($query) {
        global $wpdb;
        return str_replace(
                array('#__', '^__', '@__'), 
                array($wpdb->prefix, GMP_DB_PREF, $wpdb->prefix. GMP_DB_PREF),
                $query);
    }
    static public function getError() {
        global $wpdb;
        return $wpdb->last_error;
    }
    static public function lastID() {
        global $wpdb;        
        return $wpdb->insert_id;
    }
    static public function timeToDate($timestamp = 0) {
        if($timestamp) {
            if(!is_numeric($timestamp))
                $timestamp = dateToTimestampGmp($timestamp);
            return date('Y-m-d', $timestamp);
        } else {
            return date('Y-m-d');
        }
    }
    static public function dateToTime($date) {
        if(empty($date)) return '';
        if(strpos($date, GMP_DATE_DL)) return dateToTimestampGmp($date);
        $arr = explode('-', $date);
        return dateToTimestampGmp($arr[2]. GMP_DATE_DL. $arr[1]. GMP_DATE_DL. $arr[0]);
    }
    static public function exist($table, $column = '', $value = '') {
        if(empty($column) && empty($value)) {       //Check if table exist
            $res = self::get('SHOW TABLES LIKE "'. $table. '"', 'one');
        } elseif(empty($value)) {                   //Check if column exist
            $res = self::get('SHOW COLUMNS FROM '. $table. ' LIKE "'. $column. '"', 'one');
        } else {                                    //Check if value in column table exist
            $res = self::get('SELECT COUNT(*) AS total FROM '. $table. ' WHERE '. $column. ' = "'. $value. '"', 'one');
        }
        return !empty($res);
    }
    static public function prepareHtml($d) {
        if(is_array($d)) {
            foreach($d as $i => $el) {
                $d[ $i ] = self::prepareHtml( $el );
            }
        } else {
            $d = esc_html($d);
        }
        return $d;
    }
	static public function escape($data) {
		global $wpdb;
		return $wpdb->_escape($data);
	}
	static public function getAutoIncrement($table) {
		return (int) self::get('SELECT AUTO_INCREMENT
			FROM information_schema.tables
			WHERE table_name = "'. $table. '"
			AND table_schema = DATABASE( );', 'one');
	}
	static public function setAutoIncrement($table, $autoIncrement) {
		return self::query("ALTER TABLE `". $table. "` AUTO_INCREMENT = ". $autoIncrement. ";");
	}
}
