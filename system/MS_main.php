<?php

// here we open a class main this is the core of the system this makes sure the MVC boots up
namespace system;

// this file contains a lot of dirty code we have to improve this in the near future

use system\pipelines\MS_pipeline;
use system\router\MS_Route;
use system\router\MS_router;

class MS_main extends MS_core
{
	public $currentRequestMethod = NULL;
	public $uri                  = NULL;

	function __construct()
	{
		$this->root = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR; // we tell the core to use the full path to the msmvc root for spl
		parent::__construct();
	}
	/**
	 * @return mixed: the controller
	 */
	public function boot() {
		MS_pipeline::returnConfig('routes');
		$request                       = new MS_router();
		$request->routes               = MS_route::returnRouteCollection();
		$request->currentRequestMethod = $this->currentRequestMethod;

		if($this->currentRequestMethod !== 'CLI') {
			$request->uri = $this->uri;
		}
		$route = $request->matchRequest();

		$controllerRequest = explode('@', $route['action']['uses']);
		$controllerString = DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$controllerRequest[0];
		$controller        = new $controllerString;
		if($request->variables != NULL) {
			return call_user_func_array([$controller, $controllerRequest[1]], $request->variables);
		}
		else {
			return $controller->$controllerRequest[1]();
		}
	}


	/**
	 * we set this->uri to the current http uri
	 */
	private function setRequestUri() {
		if($this->uri === NULL) {
			$request_path = explode('?', $_SERVER['REQUEST_URI']);    //root of the URI
			$request_root = rtrim(dirname($_SERVER['SCRIPT_NAME']), '\/');    //The url
			$uri          = '/'.utf8_decode(substr(urldecode($request_path[0]), strlen($request_root) + 1));
			$this->uri = $uri;
		}
	}

	/**
	 * @throws \Exception: in case something goes wrong or a route / method isn't defined we throw an exception
	 */
	public function index() {
		$this->setRequestMethod();
		if($this->currentRequestMethod !== 'CLI') {
			$this->setRequestUri();
		}
	}

	/**
	 * @return string sets the currentRequestMethod property with the http request method
	 * @throws \Exception
	 */
	public function setRequestMethod() {
		if($this->currentRequestMethod === NULL) {
			if(php_sapi_name() == 'cli') {
				$this->currentRequestMethod = 'CLI';
			}
			else {
				$method = $_SERVER['REQUEST_METHOD'];
				switch($method) {
					case 'PUT':
						$this->currentRequestMethod = 'PUT';
						break;
					case 'POST':
						$this->currentRequestMethod = 'POST';
						break;
					case 'GET':
						$this->currentRequestMethod = 'GET';
						break;
					case 'HEAD':
						$this->currentRequestMethod = 'HEAD';
						break;
					case 'DELETE':
						$this->currentRequestMethod = 'DELETE';
						break;
					case 'OPTIONS':
						$this->currentRequestMethod = 'OPTIONS';
						break;
					default:
						throw new \Exception('The supplied request method is not supported you have used ' . $method);
						break;
				}
			}
		}
	}
}