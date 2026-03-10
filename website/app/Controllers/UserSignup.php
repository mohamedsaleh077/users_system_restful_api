<?php
declare(strict_types=1);

namespace Controllers;
use Units\User;
use Traits\Errors;
use Traits\APIHelper;
use Models\UserModel;
use Core\JWT;

/**
 * For Sign up api
 *
 * @author mohamed
 */

class UserSignup extends User
{
   private object $model;
   private array $post;
   
   use Errors;
   use APIHelper;
   
   public function __construct() 
   {
       $this->post = $this->request();
       $this->model = new UserModel();
       parent::__construct();
   }
   
   public function Create(): void
   {
      $this->CheckMethod();
      $this->CheckParams();
      $this->ValidateInput();
      $this->CheckExistance();

      $this->results["saving_results"] = $this->SaveToDatabase();
      
      $this->results["ok"] = 1;
      // setting up the token
      $this->results["jwt_token"] = $this->TokenGenerate();
      $this->Success();
   }
   
   private function CheckMethod():void
   {
       if(!$this->isPOST()){
           $this->RejectMethod();
       }
   }
   
   private function CheckParams(){
       if(!(isset($this->post["username"]) 
            && isset($this->post["password"]) 
            && isset($this->post["email"]) )){
           $this->MissingParams();
       }
   }
   
   private function ValidateInput(): void
   {
       $this->ValidateUsernameInput($this->post["username"]);
       $this->ValidateEmailInput($this->post["email"]);
       $this->ValidatePasswordInput($this->post["password"]);
       
       $this->checkValidationErrors();
   }
   
   private function CheckExistance(): void
   {
       $dbResult = $this->model->get($this->post["username"], $this->post["email"]);
       if(!empty($dbResult["results"])){
           if($dbResult["results"]["username"] === $this->post["username"]){
                $this->results["errors"][] = "This Username is used before.";
           }
            if($dbResult["results"]["email"] === $this->post["email"]){
                $this->results["errors"][] = "This Email is used before.";
            }
       }
        $this->checkValidationErrors();
   }
   
   private function HashingPassword(string $password): string
   {
       $options = [
            'cost' => 12
        ];
        return password_hash($password, PASSWORD_DEFAULT, $options);
   }
   
   private function SaveToDatabase(): array
   {
       $saveResult = $this->model->add(
              $this->post["username"], 
              $this->post["email"],
              $this->HashingPassword($this->post["password"])
              );
      
      if(!$saveResult["ok"]){
          $this->results["errors"] = "Error While Creating the account, contanct the admin";
          $this->checkValidationErrors();
      }
      
      return $saveResult;
   }
   
      private function TokenGenerate(): string
   {
       $payload = [
           "id" => $this->results["saving_results"]["lastID"],
           "username" => $this->post["username"],
           "email" =>  $this->post["email"],
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