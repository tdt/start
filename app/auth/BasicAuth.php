<?php

/**
 * Basic authentication
 */
namespace app\auth;
use app\core\Config;

class BasicAuth implements Auth{

    public function authenticate(){
        header('WWW-Authenticate: Basic realm="' . Config::get("general", "hostname") . Config::get("general", "subdir") . '"');
        header('HTTP/1.0 401 Unauthorized');
        exit();
    }


    public function isAuthenticated($user){
        if ($userconf = Config::get("auth", $user)) {
            return isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER'] == $user && $_SERVER['PHP_AUTH_PW'] == $userconf['password'];
        } else {
            return true;
        }
    }
}
