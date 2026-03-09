<?php
declare(strict_types=1);

namespace Controllers;
use Units\User;
use Traits\Errors;
use Traits\APIHelper;
use Models\UserModel;

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

      $saveResult = $this->model->add(
              $this->post["username"], 
              $this->post["email"],
              $this->HashingPassword($this->post["password"])
              );
      
      if(!$saveResult["ok"]){
          $this->results["errors"] = "Error While Creating the account, contanct the admin";
          $this->checkValidationErrors();
      }
      
      $this->results["ok"] = 1;
      // setting up the token
      $this->results["jwt_token"] = [];
      $this->Success();
   }
   
   public function CheckMethod():void
   {
       if(!$this->isPOST()){
           $this->RejectMethod();
       }
   }
   
   public function CheckParams(){
       if(!(isset($this->post["username"]) 
            && isset($this->post["password"]) 
            && isset($this->post["email"]) )){
           $this->MissingParams();
       }
   }
   
   public function ValidateInput(): void
   {
       $this->ValidateUsernameInput($this->post["username"]);
       $this->ValidateEmailInput($this->post["email"]);
       $this->ValidatePasswordInput($this->post["password"]);
       
       $this->checkValidationErrors();
   }
   
   public function CheckExistance(): void
   {
       $dbResult = $this->model->get($this->post["username"], $this->post["email"]);
       if(!empty($dbResult["results"])){
           $this->results["errors"][] = "This Username/Email is used before.";
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
   
   private function Success(){
       http_response_code(200);
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($this->results);
        die();
   }

}