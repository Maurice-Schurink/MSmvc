<?php

namespace App\system\pipelines;

/**
 * Class MS_pipeline
 * @package system\pipelines
 */
class MS_filesystem {

	/**
	 * the existing datasets on name assoc
	 * @var array
	 */
	private static $dataSets;

	/**
	 * path content to include
	 * @var array
	 */
	private $requestedDataSet;

	/**
	 * @var bool
	 */
	private $file;

	/**
	 * @var
	 */
	private $path;

	/**
	 * this will be used if the included path is not possible
	 * @var
	 */
	public static $root;


	/**
	 * MS_pipeline constructor.
	 * todo: make pipeline a filemanger and use change dir to change between diretories!
	 * this is the file that is requested
	 *
	 * @param null|string $requestDataSet
	 *
	 * @internal param null $requestData
	 */
	function __construct($path = NULL) {
		if (!is_null($path)) {
			$this->setPath($path);
			$this->setFile();
		}
	}

	public function setPath(string $path) {
		$this->path = $this->cleanPath($path);
	}

	/**
	 * @param $path
	 *
	 * @return mixed
	 */
	public function cleanPath($path) {
		return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
	}

	public function include () {
		if ($this->isFile() == true) {
			//basic include
		} else {
			//include as dir
		}
	}

	private function includeAsFile(bool $once = false) {
		if ($once == true) {
			include_once($this->path);
		} else {
			include($this->path);
		}
	}

	public function include_once () {

	}

	/**
	 * @param            $file
	 * @param array|NULL $data
	 *
	 * @return string
	 */
	public static function executeAndReturnFileContent($file, array $data = NULL) {
		if (is_array($data)) {
			extract($data, EXTR_SKIP);
		}
		ob_start();
		include $file;
		return ob_get_clean();
	}

	/**
	 * @param $file
	 *
	 * @return array
	 */
	public static function getClassesWithinFile($file) {
		$php_code = file_get_contents($file);
		$classes = [];
		$tokens = token_get_all($php_code);
		$count = count($tokens);
		for ($i = 2; $i < $count; $i++) {
			if ($tokens[$i - 2][0] == T_CLASS && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING) {

				$class_name = $tokens[$i][1];
				$classes[] = $class_name;
			}
		}
		return $classes;
	}

	/**
	 * Determine if a file exists.
	 *
	 * @param  string $path
	 *
	 * @return bool
	 */
	public function exists($path) {
		return file_exists($path);
	}

	/**
	 * Get the contents of a file.
	 *
	 * @param  string $path
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function getContent(string $path) {
		if ($this->isFile($path)) {
			return file_get_contents($path);
		}
		throw new \Exception("File does not exist at path $path");
	}

	/**
	 * @param bool $force
	 *
	 * @return int|mixed
	 */
	public function getDataSetFromRequest(bool $force = FALSE) {
		if (!isset(self::$dataSets[$this->getRequestedDataSet()["filename"]]) || $force === TRUE) {


			switch ($this->getRequestedDataSet()["extension"]) {
				case 'php':
					return $this->openPhpFile();
					break;
				case 'json':
					return $this->openJsonFile();
					break;
				default:
					return $this->basicIncludeFile();
					break;
			}
		} else {
			return self::$dataSets[$this->getRequestedDataSet()["filename"]];
		}
	}

	/**
	 * @return mixed
	 */
	private function basicIncludeFile() {
		return file_get_contents($this->getRequestedDataSet()["requestFile"], FILE_USE_INCLUDE_PATH);
	}

	/**
	 * @return mixed
	 */
	protected function openPhpFile() {
		return include $this->getRequestedDataSet()["requestFile"];
	}

	/**
	 * @param $file
	 *
	 * @return string
	 */
	public static function returnViewFilePath($file) {
		return self::$root . 'resources/views/' . $file . '.php';
	}

	/**
	 * @param $file
	 *
	 * @return string
	 */
	public static function returnLayoutFilePath($file) {
		return self::$root . 'resources/views/layouts/' . $file . '.php';
	}

	/**
	 * @return mixed
	 */
	private function openJsonFile() {
		return json_decode(file_get_contents(self::$root . 'config' . DIRECTORY_SEPARATOR . $this->requestedDataSet . '.json', FILE_USE_INCLUDE_PATH), TRUE);
	}

	/**
	 * @param string $filename
	 *
	 * @return string
	 */
	public static function getFileContent(string $filename) {
		return file_get_contents(self::$root . $filename);
	}

	/**
	 * @return null
	 */
	public function getRequestedDataSet() {
		return $this->requestedDataSet;
	}

	/**
	 * @param array $requestedDataSet
	 */
	public function setRequestedDataSet(array $requestedDataSet) {
		$this->requestedDataSet = $requestedDataSet;
	}

	/**
	 * @return bool
	 */
	public function isFile() {
		return $this->file;
	}

	public function setFile() {
		$this->file = is_file($this->path);
	}
}

// todo: make a pipeline sublayer to interacte with data providers
// todo: database config files support
/*
	//todo: improve the include location
	//todo: add folder inclusion
// todo: add documentation
*/