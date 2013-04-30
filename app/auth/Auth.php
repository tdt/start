<?php
/**
 * Authentication interface
 */

namespace app\auth;

interface Auth{
    public function authenticate();
    public function isAuthenticated($user);
}
