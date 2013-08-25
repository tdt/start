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

// Fetch the routes from the config
$allroutes = app\core\Config::get("routes");


// Only keep the routes that use the requested HTTP method
$unsetkeys = preg_grep("/^" . strtoupper($_SERVER['REQUEST_METHOD']) . "/", array_keys($allroutes), PREG_GREP_INVERT);
foreach ($unsetkeys as $key) {
    unset($allroutes[$key]);
}

$routes = array();
// Drop the HTTP method from the route
foreach ($allroutes as $route => $controller) {
    $route = preg_replace('/^' . strtoupper($_SERVER['REQUEST_METHOD']) . '(\s|\t)*\|(\s|\t)*/', "", trim($route));
    $routes[trim($route)] = trim($controller);
}

//$log->logInfo("The routes we are working with", $routes);

try {
    // This function will do the magic.
    Glue::stick($routes);
} catch (Exception $e) {

    // Instantiate a Logger
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