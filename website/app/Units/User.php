<?php
declare(strict_types=1);

namespace Units;

use const FILTER_VALIDATE_EMAIL;
use function filter_var;
use function strlen;

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

    protected function ValidateUsernameInput($username): void
    {
        if(strlen($username) > 50){
            $this->results["errors"][] = "Username excuted the max length 50char";
        }
        if(strlen($username) < 3){
            $this->results["errors"][] = "Username can't be less than 3 char";
        }
        if(preg_match("#[^a-zA-Z0-9/._-]#", $username)){
            $this->results["errors"][] = "username must be a-zA-Z._-";
        }
    }
    
    protected function ValidateEmailInput($email): void
    {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $this->results["errors"][] = "Invalid Email";
        }
        if(strlen($email) > 255){
            $this->results["errors"][] = "Email is longer than 255";
        }
    }
    protected function ValidatePasswordInput($password): void
    {
        if(strlen($password) > 50){
            $this->results["errors"][] = "Password excuted the max length 50char";
        }
        if(strlen($password) < 6){
            $this->results["errors"][] = "Password can't be less than 6 chars.";
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
