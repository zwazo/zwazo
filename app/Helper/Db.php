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
	 * database.default.adapter = pdo_mysql
    * database.default.params.host = db1538.1and1.fr 
    * database.default.params.username = dbo249106289
    * database.default.params.password = 7conDios5T
    * database.default.params.dbname = db249106289
	 */
	public static function getInstance() {
		if (is_null(self::$_instance)) {
			self::$_instance = new \PDO(
				'mysql:dbname=db249106289;host=db1538.1and1.fr'
				,'dbo249106289'
				,'7conDios5T' // password
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