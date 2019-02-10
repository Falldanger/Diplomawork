<?php
class dateGmp {
	static public function _($time = NULL) {
		if(is_null($time)) {
			$time = time();
		}
		return date(GMP_DATE_FORMAT_HIS, $time);
	}
}