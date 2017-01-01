<?php
// here we open a class main this is the core of the system this makes sure the MVC boots up

// this file contains a lot of dirty code we have to improve this in the near future

use system\MS_request;
use system\pipelines\MS_pipeline;
use system\router\MS_route;
use system\router\MS_router;

/**
 * Class MS_start: this class will start the framework
 * @package system: MSmvc main
 * @author  Maurice Schurink
 */
class MS_start {
	public $currentRequestMethod = NULL;
	public $uri = NULL;

	/**
	 * MS_start constructor.
	 */
	public function __construct() {
		MS_pipeline::$root = dirname(__FILE__) . DIRECTORY_SEPARATOR;
		//	set_exception_handler([new MS_handler, 'exceptionHandler']);
		//	set_error_handler([new MS_handler, 'errorHandler']);
		//	register_shutdown_function([new MS_handler, 'fatal_handler']);
	}

	/**
	 * we let the router run to find the right route to use then we pass it to the request so the controller can be
	 * called followed by the response
	 */
	public function boot() {
		$this->setRequestMethod();
		if($this->currentRequestMethod !== 'CLI') {
			$this->setRequestUri();
		}
		$request = new MS_request();
		$request->requestInterface = $this->currentRequestMethod;

		foreach(glob(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config\*.php') as $filename) {
			MS_pipeline::getConfigFileContent($filename, 'basic');
		}
		$router = new MS_router();
		$router->routes = MS_route::returnRouteCollection();
		$router->currentRequestMethod = $this->currentRequestMethod;
		if($this->currentRequestMethod !== 'CLI') {
			$router->uri = $this->uri;
		}
		$request->requestRoute = $router->matchRequest();
		if($router->variables !== NULL) {
			$request->requestVariables = $router->variables;
		}
		$request->request();
	}

	/**
	 * we set this->uri to the current http uri
	 */
	private function setRequestUri() {
		if($this->uri === NULL) {
			$request_path = explode('?', $_SERVER['REQUEST_URI']);    //root of the URI
			$request_root = rtrim(dirname($_SERVER['SCRIPT_NAME']), '\/');    //The url
			$uri = '/' . utf8_decode(substr(urldecode($request_path[0]), strlen($request_root) + 1));
			$this->uri = $uri;
		}
	}


    /**
     * this function will set the request method
     */
    private function setRequestMethod() {
		if($this->currentRequestMethod === NULL) {
			if(php_sapi_name() == 'cli') {
				$this->currentRequestMethod = 'CLI';
			}
			else {
                if (!empty($_REQUEST['method'])) {
                    $this->currentRequestMethod = $_REQUEST['method'];
                } else {
                    $this->currentRequestMethod = $_SERVER['REQUEST_METHOD'];
                }
			}
		}
	}
}