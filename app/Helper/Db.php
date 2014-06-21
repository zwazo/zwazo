<?php

namespace App\Helper;

Class Db {

	private static $_instance = null;

	/**
	 *
	 */
	private function __construct() {
		/* only statics */
	}
	
	/**
	 *
	 */
	public static function getInstance() {
		if (is_null(self::$_instance)) {
			self::$_instance = new \PDO(
				'mysql:dbname=zwazo;host=127.0.0.1'
				,'root'
				,'' // password
			);
		}
		return self::$_instance;
	}
	
	/**
	 *
	 */
	public static function close() {
		self::$_instance = null;
	}
	
	/**
	 *
	 */
	public static function query($sql, $vars=null) {
		$stmt = self::getInstance()->prepare($sql);
		if (false === $stmt) {
			throw new Exception('Unable to perform query preparation');
		}
		if ( !$stmt->execute($vars) ) {
			// return false;
		}
		return $stmt;
	}
	
	/**
	 *
	 */
	public static function lastInsertId() {
		return self::getInstance()->lastInsertId();
	}
	
}