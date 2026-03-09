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
   }
   
   public function CheckMethod():void
   {
       if(!$this->isPOST()){
           $this->RejectMethod();
       }
   }
   
   public function CheckParams(){
       if(!(isset($this->post["username"]) && isset($this->post["password"]) 
            && isset($this->post["email"]) )){
           $this->MissingParams();
       }
   }
   
   public function ValidateInput(): void
   {
       $this->ValidateUsernameInput($post["username"]);
       $this->ValidateEmailInput($post["email"]);
       $this->ValidatePasswordInput($post["password"]);
       
       $this->checkValidationErrors();
   }
   
   public function CheckExistance(): void
   {
       $dbResult = $this->model->get($post["username"], $post["email"]);
       if(!empty($dbResult["results"])){
           $this->results["errors"][] = "This Username/Email is used before.";
       }
        $this->checkValidationErrors();
   }

}