<?php

namespace App\system;

/**
 * Class Request
 * @package MSmvc\system
 */
class Request {
    public $requestInterface = 'HTTP';
    public $requestInterfaceInformation = NULL;
    public $realRequestInterface = 'HTTP';
    public $requestMethod = 'GET';

    public $requestRoute;
    public $requestVariables = [];

    private $response;

    /**
     * we open the MS_functions that way we always will be able to use the functions
     * we will prepare the response when the request is started that way we can change it at any point
     */
    function __construct() {
        $this->response = new Response();
    }

    /**
     * This method will start the controller and execute it it's a void method so we don't expect any return values
     * todo: fix the controller loading current version doesn't support psr-4 fix the namespaces
     * The controller should use functions provided by MS_functions to send data to the response object
     */
    private function callController() {
        $controllerRequest = explode('@', $this->requestRoute['action']['uses']);
        $controllerString = 'App' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $controllerRequest[0];
        $controller = new $controllerString;

        call_user_func_array([$controller, $controllerRequest[1]], $this->requestVariables);
    }

    /**
     * We will do a simple blacklist check and then execute the controller and return our response
     */
    public function request() {
        if(is_callable($this->requestRoute['action'])){
            echo $this->requestRoute['action']();
        }
        elseif(!empty($this->requestRoute['action']['uses'])) {
            $this->callController();
        }
        $this->response->returnResponse();
    }
    //make a response overwrite to cancel the response
}