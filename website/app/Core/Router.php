<?php
declare(strict_types=1);

namespace Core;
use Core\View;
use Traits\Errors;


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
    
    use Errors;
    
    public function __construct() {
        $this->method = $_SERVER["REQUEST_METHOD"];
        $this->viewer = new View();
    }   
    
    public function home(){
        $routes = [
            "GET /users/ -"
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
            $this->PageNotFound();
        }
        
        if(!in_array( $this->method, $ALLOWED_ACTIONS[$action])){
            $this->RejectMethod();
        }
        $action = "user_" . $action;
        $this->$action();
    }
    
    private function user_index():void
    {
        $routes = [
            "GET/POST : /user/signup",
            "GET/POST : /user/login"
        ];
        $this->viewer::RoutesView("Users", $routes);
    }
    
    private function user_signup() {
        if($this->method === "GET"){
            $routes = [
              "POST : /user/signup {username:50 password:50 email:255}"  
            ];
            $this->viewer::RoutesView("User Sign up", $routes);
        }
        if($this->method === "POST"){
            call_user_func_array([(new \Controllers\UserSignup()), "create"], []);
        }
    }
    
}
