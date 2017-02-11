<?php
/**
 * Setting routes is easy with the use of MS_route
 * we can use get, post, any, patch, delete, and options to set a new route this will this allows us to set the request
 * methods to match besides the uri cli is also available and will handle the command line requests
 *
 * IMPORTANT don't use the -m key for the cli requests since this is used for method identification
 *
 * example MS_route::get('blog/index')
 *
 * first parameter is the URI to match if you place a part between {}
 * it wil be seen as a wildcard and as long as it is filled.
 * It will match and return this value to the controller
 *
 * second parameter is an array which holds the items that we use apply to the matched route
 * currently we only support uses and as in this array
 *
 * uses: holds the controller with the method to call separated with @
 * as: holds the name of the controller so you can use the name to call a controller.
 * parameters: the parameters to be used for the request only used by the cli method
 */
use App\system\router\MS_route;

/*MS_route::any('/', function (){
    return 123;
});
*/
MS_route::any('/', ['uses' => 'example@index', 'as' => 'home']);

MS_route::get('/generate', ['uses' => 'generate@index', 'as' => 'generateFormPage']);
//MS_route::get('/generate/model/{id}',['uses' => 'generate@requestModelContent']);

MS_route::any('/test',['uses' => 'tableControllerName@index']);

//MS_route::cli('model', ['uses' => 'generate@generateModel', 'as' => 'modelGen', 'parameters' => 'n:']);
//MS_route::cli('controller', ['uses' => 'generate@generateController', 'as' => 'controllerGen', 'parameters' => 'n:']);