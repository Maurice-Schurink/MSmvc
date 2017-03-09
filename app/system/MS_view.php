<?php

namespace App\system;

/**
 * Class MS_view
 * @package system
 */
class MS_view {
	/**
	 * view: name of the view file
	 * data: array with the variables
	 * @var array
	 */
	private static $view;

	/**
	 * html content of the view file in case of a layout
	 * @var mixed
	 */
	public static $viewHtml;

	/**
	 * the name of the layout
	 * @var string
	 */
	private static $layout;

	/**
	 * @return string
	 */
	public static function getLayout(): string {
		return self::$layout;
	}

	/**
	 * @param string $layout
	 */
	public static function setLayout(string $layout = null) {
		if ($layout !== NULL) {
			self::$layout = str_replace(".php", "", $layout) . ".php";
		}
	}

	/**
	 * @return array
	 */
	public static function getView(): array {
		return self::$view;
	}

	/**
	 * @param array $view
	 */
	public static function setView(array $view) {
		$view["view"] = str_replace(".php", "", $view["view"]) . ".php";
		self::$view = $view;
	}

	public function loadView() {
		$viewFile = new MS_filesystem(self::getView()['view'], MS_filesystem::USE_VIEW_PATH);
		$viewFile->setLocalData(self::getView()['data']);
		self::$viewHtml = $viewFile->executeAndReturn();
		if (self::$layout !== NULL) {
			$viewFile = new MS_filesystem(self::getLayout(), MS_filesystem::USE_LAYOUT_PATH);
			$viewFile->include();
		} else {
			echo self::$viewHtml;
		}
	}

	/**
	 * @param string $viewName
	 */
	public static function loadPartial(string $viewName) {
		$view = MS_filesystem::returnViewFilePath($viewName);
		include $view;
	}
}