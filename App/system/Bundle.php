<?php

namespace App\system;

use App\system\pipelines\MS_pipeline;

class Bundle
{
	private static $scriptSet;
	private static $styleSet;

	/**
	 * @param $masterview : the name of the masterview to return
	 *
	 * @return array|null: we will return all the stylesheets for this view
	 */
	public static function returnStyleCollection($masterview) {
		if(isset(self::$styleSet['*']) && isset(self::$styleSet[$masterview])) {
			return array_merge(self::$styleSet['*'], self::$styleSet[$masterview]);
		}
		elseif(isset(self::$styleSet['*'])) {
			return self::$styleSet['*'];
		}
		elseif(isset(self::$styleSet[$masterview])) {
			return self::$styleSet[$masterview];
		}
		else {
			return NULL;
		}
	}

	/**
	 * @param $masterview : the name of the masterview to return
	 *
	 * @return array|null: we will return all the scripts for this masterview
	 */
	public static function returnScriptCollection($masterview) {
		if(isset(self::$scriptSet['*']) && isset(self::$scriptSet[$masterview])) {
			return array_merge(self::$scriptSet['*'], self::$scriptSet[$masterview]);
		}
		elseif(isset(self::$scriptSet['*'])) {
			return self::$scriptSet['*'];
		}
		elseif(isset(self::$scriptSet[$masterview])) {
			return self::$scriptSet[$masterview];
		}
		else {
			return NULL;
		}
	}

	/**
	 * @param       $path          : the path to the javascript file
	 * @param array $masterViewSet : the master view to append it to note using * will apply it to all the masters
	 * @param bool  $relative      : if we will prefix the path with public / scripts
	 */
	public static function javascript($path, $masterViewSet = ['*'], $relative = TRUE) {
		$prefix = $relative === TRUE ? MS_pipeline::$root.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'scripts'.DIRECTORY_SEPARATOR : '';
		foreach($masterViewSet as $value) {
			self::$scriptSet[$value][] = $prefix . $path;
		}
	}

	/**
	 * this method will add a stylesheet to the collection of the masterview
	 *
	 * @param       $path          : the path to the stylesheet
	 * @param array $masterViewSet : the master view to append it to note using * will apply it to all the masters
	 * @param bool  $relative      : if we will prefix the path with public / stylesheets
	 */
	public static function stylesheet($path, $masterViewSet = ['*'], $relative = TRUE) {
		$prefix = $relative === TRUE ? MS_pipeline::$root.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'stylesheets'.DIRECTORY_SEPARATOR : '';
		foreach($masterViewSet as $value) {
			self::$styleSet[$value][] = $prefix . $path;
		}
	}
}