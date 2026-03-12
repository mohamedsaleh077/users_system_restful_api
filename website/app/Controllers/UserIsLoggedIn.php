<?php

namespace Controllers;
use Units\User;

/**
 * Description of UserIsLoggedIn
 *
 * @author mohamed
 */
class UserIsLoggedIn extends User
{
    public function Check(){
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($this->GetLogin());
        die();
    }
}
