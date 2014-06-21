<?php

namespace App\Helper;

Class Utils {

	public static function printr( $mixed ) {
		echo '<pre>'.print_r($mixed, true).'</pre>';
	}

}