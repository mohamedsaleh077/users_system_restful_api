<?php
declare(strict_types=1);

namespace Traits;
/**
 * for All Validations and connection checks for API
 *
 * @author mohamed
 */
Trait APIHelper {
    public function request(): array
    {
        $method = $_SERVER["REQUEST_METHOD"];
        if($method === "GET"){
            return $_GET;
        }
        return json_decode(file_get_contents("php://input"), true);
    }
    
    public function isGET(): bool
    {
        if($_SERVER["REQUEST_METHOD"] === "GET"){
            return true;
        }
        return false;
    }
    
    
    public function isPOST(): bool
    {
        if($_SERVER["REQUEST_METHOD"] === "POST"){
            return true;
        }
        return false;
    }
    
    
    public function isPUT(): bool
    {
        if($_SERVER["REQUEST_METHOD"] === "PUT"){
            return true;
        }
        return false;
    }
    
    
    public function isDELETE(): bool
    {
        if($_SERVER["REQUEST_METHOD"] === "DELETE"){
            return true;
        }
        return false;
    }
}
