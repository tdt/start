<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

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
} catch (tdt\exceptions\TDTException $e) {
    $log = new Logger('router');
    $log->pushHandler(new StreamHandler(app\core\Config::get("general", "logging", "path") . "/log_" . date('Y-m-d') . ".txt", Logger::ERROR));
    $log->addError($e->getMessage());
    set_error_header($e->getCode(), $e->getShort());
    echo "<script>location = \"" . $e->getURL() . "\";</script>";
} catch (Exception $e) {
    $log = new Logger('router');
    $log->pushHandler(new StreamHandler(app\core\Config::get("general", "logging", "path") . "/log_" . date('Y-m-d') . ".txt", Logger::CRITICAL));
    $log->addCritical($e->getMessage());
    set_error_header(500, "Internal Server Error");
    //add a javascript redirect to an error page
    echo "<script>location = \"" . app\core\Config::get("general", "hostname") . app\core\Config::get("general", "subdir") . "error/critical/\";</script>";
}