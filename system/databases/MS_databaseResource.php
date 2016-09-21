<?php
/**
 * Created by PhpStorm.
 * User: Maurice
 * Date: 25-5-2016
 * Time: 11:19
 */

namespace system\databases;

use system\pipelines\MS_pipeline;

class MS_databaseResource {
	private static $dataBaseResourceSet = [];
	private static $defaultConnectionName;

	/**
	 * @return mixed
	 */
	public static function getDefaultConnectionName() {
		if(empty(self::$dataBaseResourceSet)){
			MS_pipeline::returnConfig('database');
		}
		return self::$defaultConnectionName;
	}

	/**
	 * @param mixed $defaultConnectionName
	 */
	public static function setDefaultConnectionName($defaultConnectionName) {
		self::$defaultConnectionName = $defaultConnectionName;
	}

	/**
	 * @return array: return all the database resources indexed by the autoincrement.
	 */
	public static function getDataBaseResourceSet() {
		return self::$dataBaseResourceSet;
	}

	//this will be used for the database resource same like the route
	public static function create(array $databaseSettings) {
		if(!empty($databaseSettings['name']) && !empty($databaseSettings['settings'])) {
			self::$dataBaseResourceSet[$databaseSettings['name']] = $databaseSettings['settings'];
		}
		else {
			throw new \Exception('there is no name for this connection');
		}
	}
}