<?php
declare(strict_types=1);

namespace Core;
use Core\View;

/**
 * Description of Router
 *  Decide what to do based on the Action and Params of the Route
 *      Route / Action / Params
 *  Route is the method that App Class is calling
 * @author mohamed
 */
class Router {
    
    private string $method;
    private object $viewer;
    
    public function __construct() {
        $this->method = $_SERVER["REQUEST_METHOD"];
        $this->viewer = new View();
    }
    
    public function home(){
        $routes = [
            "GET /users"
        ];
        $this->viewer::RoutesView("Home", $routes);
    }

    public function user(string $action = "index"):void
    {
        $ALLOWED_ACTIONS = [
            "index" => ["GET"],
            "signup" => ["GET", "POST"],
            "login" => ["GET", "POST"]
        ];
        
        if(!in_array( $action, array_keys($ALLOWED_ACTIONS))){
            ; // err 404
        }
        
        if(!in_array( $this->method, $ALLOWED_ACTIONS[$action])){
            ; // err 405 method not allowed
        }
        $action = "user_" . $action;
        $this->$action();
    }
    
    private function user_index():void
    {
        $routes = [
            "GET/POST : /user/signu",
            "GET/POST : /user/login"
        ];
        $this->viewer::RoutesView("Users", $routes);
    }
    
}
