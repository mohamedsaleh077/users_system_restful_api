<?php
declare(strict_types=1);

namespace Units;

/**
 * 
 * The Parent of all users
 *
 * @author mohamed
 */
class User {
    protected ?array $results = [];
    
    public function __construct() {
        $this->results = [
            "ok" => 1,
            "errors" => []
        ];
    }

    protected function ValidateUsernameInput(string $username): void
    {
        if(strlen($username) > 50){
            $this->results["errors"]["username"][] = "Username excuted the max length 50char";
        }
        if(strlen($username) < 3){
            $this->results["errors"]["username"][] = "Username can't be less than 3 char";
        }
        if(preg_match("#[^a-zA-Z0-9/._-]#", $username)){
            $this->results["errors"]["username"][] = "username must be a-zA-Z._-";
        }
    }
    
    protected function ValidateEmailInput(string $email): void
    {
       $pattern = '/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
       
        if(!preg_match($pattern, $email)){
            $this->results["errors"]["email"][] = "Invalid Email";
        }
        if(strlen($email) > 255){
            $this->results["errors"]["email"][] = "Email is longer than 255";
        }
        if(strlen($email) < 8){
            $this->results["errors"]["email"][] = "Email is too shorter than 8 chars";
        }
    }
    protected function ValidatePasswordInput(string $password): void
    {
        if(strlen($password) > 50){
            $this->results["errors"]["password"][] = "Password excuted the max length 50char";
        }
        if(strlen($password) < 6){
            $this->results["errors"]["password"][] = "Password can't be less than 6 chars.";
        }
    }

    protected function checkValidationErrors(): void
    {
        if (!empty($this->results["errors"])){
            $this->results["ok"] = 0;
        }
        $this->showErrors();
    }
    
    protected function showErrors(){
       if(!$this->results["ok"]){
           $this->ValidationError($this->results["errors"]);
       }
    }
}
