<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Controllers;

use Core\JWT;
use Models\UserModel;
use Units\User;

use Traits\Errors;
use Traits\APIHelper;
use function password_verify;


/**
 * Description of UserLogin
 *
 * @author mohamed
 */
class UserLogin extends User
{
    private array $post;
    private object $model;
    private bool $isEmail = false;
    private array $userData;
    
    use Errors;
    use APIHelper;
   
    public function __construct() {
        $this->post = $this->request();
        $this->model = new UserModel();
        parent::__construct();
    }
    
    public function Login(){
        $this->CheckParams();
        $this->ValidateInput();
        $this->CheckExistance();
        
        if(password_verify($this->post["password"], 
                           $this->userData["results"]["password_hash"])){
            $this->results["errors"]["password"][] = "Invalid Password.";
        }
        
        $this->checkValidationErrors();
        
        $this->results["ok"] = 1;
        
        $this->results["jwt_token"] = $this->TokenGenerate();
        $this->Success();
    }
    
    private function CheckParams(){
       if(!(isset($this->post["username_email"]) 
            && isset($this->post["password"])  )){
           $this->MissingParams();
       }
   }
   
   private function ValidateInput(): void
   {
       if(filter_var($this->post["username_email"], FILTER_VALIDATE_EMAIL)){
          $this->ValidateEmailInput($this->post["username_email"]);
          $this->isEmail = true;
       } else {
           $this->ValidateUsernameInput($this->post["username_email"]);
       }
       $this->ValidatePasswordInput($this->post["password"]);

       $this->checkValidationErrors();
   }
   
   private function CheckExistance(): void
   {
       $this->userData = $this->model->getAll(
               $this->post["username_email"], 
               $this->post["username_email"]
               );

       if(empty($this->userData["results"])){
           if($this->isEmail){
               $this->results["errors"][] = "This Email is not exists.";
           }
           $this->results["errors"][] = "This Username is not exists.";
       }
       
       $this->checkValidationErrors();
   }
   
   private function TokenGenerate(): string
   {
       $payload = [
           "id" => $this->userData["results"]["id"],
           "username" => $this->userData["results"]["username"],
           "email" =>  $this->userData["results"]["email"],
           "created_at" => time() 
       ];
       
       $jwt = new JWT();
       
       return $jwt->Encode($payload);
   }
   
   private function Success(): void
   {
        $cookie_options = [
            'expires' => time() + 3600,
            'domain' => "",
            'path' => '/',
            'secure' => false,
            'httponly' => true,
            'samesite'=> 'Lax'
        ];
       setcookie("Token", $this->results["jwt_token"] , $cookie_options);
       http_response_code(200);
       header("Content-Type: application/json; charset=utf-8");
       echo json_encode($this->results);
       die();
   }
}
