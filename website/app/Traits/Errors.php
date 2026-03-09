<?php

declare(strict_types=1);
namespace Traits;

/**
 * global error handler
 * @author mohamed
 */
trait Errors{
    
    public function PageNotFound(): void
    {
        http_response_code(404);
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode(["ok" => 0, "error" => "Page Not Found."]);
        die();
    }
    
    public function Reject() :void
    {
        http_response_code(405);
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode(["ok" => 0, "error" => "This method is not Allowed"]);
        die();
    }
    
    public function MissingParams() :void
    {
        http_response_code(400);
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode(["ok" => 0, "error" => "Missing Params."]);
        die();
    }
    
    public function ValidationError(array $details = []):void
    {
        http_response_code(422);
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode([
                            "ok" => 0, 
                            "error" => "invalide inputs",
                            "errors" => $details
                        ]);
        die();
    }
}
