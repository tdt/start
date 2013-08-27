<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use tdt\pages\Generator;

/**
 * This file is the router. It's where all calls come in.
 * It will accept a request and start the right controller.
 *
 * @copyright (C) 2013 by OKFN Belgium
 * @license AGPLv3
 * @author Pieter Colpaert
 * @author Jan Vansteenlandt
 * @author Michiel Vancoillie
 */
require_once APPPATH . "core/glue.php";


// Support for CGI/FastCGI
if (!isset($_SERVER["REQUEST_URI"])) {
    $_SERVER["REQUEST_URI"] = substr($_SERVER["PHP_SELF"], 1);
    if (isset($_SERVER["QUERY_STRING"])) {
        $_SERVER["REQUEST_URI"] .= "?" . $_SERVER["QUERY_STRING"];
    }
}

// Drop subdir from the request
$subdir = app\core\Config::get("general", "subdir");
if (!empty($subdir)) {
    try {
        $_SERVER["REQUEST_URI"] = preg_replace("/^\/?" . str_replace('/', '\/', $subdir) . "/", "", $_SERVER["REQUEST_URI"]);
    } catch (Exception $e) {
        // Couldn't convert subdir to a regular expression
    }
}

// Fetch the routes from the config
$allroutes = app\core\Config::get("routes");


// Only keep the routes that use the requested HTTP method
foreach ($allroutes as $key => $route){
    if (strcmp($route['method'],strtoupper($_SERVER['REQUEST_METHOD'])) != 0){
        unset($allroutes[$key]);
    }
}

try {
    // This function will do the magic.
    Glue::stick($allroutes);
} catch (tdt\exceptions\TDTException $e) {
    $log = new Logger('router');
    $log->pushHandler(new StreamHandler(app\core\Config::get("general", "logging", "path") . "/log_" . date('Y-m-d') . ".txt", Logger::ERROR));

    // Generator to generate an error page
    $generator = new Generator();
    $generator->setTitle("The DataTank");

    if($e instanceof tdt\exceptions\TDTException){
        // DataTank error
        $log->addError($e->getMessage());
        set_error_header($e->getCode(), $e->getShort());
        if($e->getCode() < 500){
            $generator->error($e->getCode(), "Sorry, but there seems to be something wrong with the call you've made", $e->getMessage());
        }else{
            $generator->error($e->getCode(), "Sorry, there seems to be something wrong with our servers", "If you're the system administrator, please check the logs. Otherwise, check back in a short while.");
        }
    }else{
        // General error
        $log->addCritical($e->getMessage());
        set_error_header(500, "Internal Server Error");
        $generator->error($e->getCode(), "Sorry, there seems to be something wrong with our servers", "If you're the system administrator, please check the logs. Otherwise, check back in a short while.");
    }

    exit(0);
}