<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use JsonSchema\Validator;

/**
 *       Booting...
 * [================>  ]
 *
 * @copyright (C) 2013 by OKFN Belgium
 * @license AGPLv3
 * @author Michiel Vancoillie
 * @author Jan Vansteenlandt
 * @author Pieter Colpaert
 */

// Autoload dependencies with composer (PSR-0)
require_once VENDORPATH . "autoload.php";

// Load the configurator
require_once APPPATH . "core/configurator.php";

// Load the configuration wrapper
require_once APPPATH . "core/Config.php";

// Load auth classes
require_once APPPATH . "auth/Auth.php";
require_once APPPATH . "auth/BasicAuth.php";


// Support for CGI/FastCGI
if (!isset($_SERVER["REQUEST_URI"])) {
    $_SERVER["REQUEST_URI"] = substr($_SERVER["PHP_SELF"], 1);
    if (isset($_SERVER["QUERY_STRING"])) {
        $_SERVER["REQUEST_URI"] .= "?" . $_SERVER["QUERY_STRING"];
    }
}

// Keep cores as the last item
$config_files = array(
    "general",
    "db",
    "tdtext",
    "auth"
);

$config_validator = new Validator();
// Check if all config files are present and validate them
foreach($config_files as $file){
    $filename = APPPATH. "config/". $file . ".json";
    $schema = APPPATH. "config/schema/". $file . "-schema.json";

    if(!file_exists($filename)){
        echo "The file $file doesn't exist. Please check whether you have copied ". APPPATH ."config/$file.example.json to ".$filename;
        exit();
    }elseif(file_exists($schema)){
        // Validate config file if schema exists
        $config_validator->check(json_decode(Configurator::stripComments(file_get_contents($filename))), json_decode(file_get_contents($schema)));

        if (!$config_validator->isValid()) {
            echo "JSON ($file.json) does not validate. Violations:\n";
            foreach ($config_validator->getErrors() as $error) {
                echo sprintf("[%s] %s\n",$error['property'], $error['message']);
            }
            exit();
        }
    }
}

// Start loading config files
$config = "";

try{
    $config = Configurator::load($config_files);
}catch(Exception $e){
    // TODO: show nice error page
    echo $e->getMessage();
    exit();
}

//TODO: handle authentication over here

// Pass on the configuration

app\core\Config::setConfig($config);

// Initialize the timezone
date_default_timezone_set(app\core\Config::get("general","timezone"));

// General getallheaders function
if (!function_exists("getallheaders" )){
    function getallheaders(){
        foreach ($_SERVER as $name => $value){
            if (substr($name, 0, 5) == "HTTP_" ) {
                $headers[str_replace( " ", "-", ucwords(strtolower(str_replace("_" , " " , substr($name, 5)))))] = $value;
            } else if ($name == "CONTENT_TYPE") {
                $headers["Content-Type"] = $value;
            } else if ($name == "CONTENT_LENGTH") {
                $headers["Content-Length"] = $value;
            }
        }
        return $headers;
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

//unset authentication: the router doesn't have to know anything about the authentication mechanism. This is tdt/start's problem.
unset($config["auth"]);

// Start the engines
$core = new tdt\core\Router($config);
$core->run();

// Hacking the brains of other people using fault injection
// http://jlouisramblings.blogspot.dk/2012/12/hacking-brains-of-other-people-with-api.html
if(app\core\Config::get("general", "faultinjection", "enabled")){
    // Return a 503 Service Unavailable ~ each {period} requests and add Retry-After header
    if(! rand(0, app\core\Config::get("general", "faultinjection", "period") -1) ){
        set_error_header("503","Service Unavailable");
        header("Retry-After: 0");
        exit();
    }
}

// Prepare the error handler defined at the end of this file
set_error_handler("wrapper_handler");


// TODO: add Tracker

/**
 * This function is called when an unexpected error(non-exception) occurs
 * @param integer $number Number of the level of the error that's been raised.
 * @param string  $string Contains errormessage.
 * @param string  $file   Contains the filename in which the error occured.
 * @param integer $line   Represents the linenumber on which the error occured.
 * @param string  $context Context is an array that points to the active symbol table at the point the error occurred. In other words, errcontext will contain an array of every variable that existed in the scope the error was triggered in. User error handler must not modify error context.
 */
function wrapper_handler($number, $string, $file, $line, $context){
    global $log;

    $error_message = $string . " on line " . $line . " in file ". $file . ".";
    $log = new Logger('bootstrap');
         $log->pushHandler(new StreamHandler(app\core\Config::get("general", "logging", "path") . "/log_" . date('Y-m-d') . ".txt", Logger::ERROR));
         $log->addError($error_message);
    echo "<script>location = \"" . app\core\Config::get("general","hostname") . app\core\Config::get("general","subdir") . "error/critical\";</script>";
    set_error_header(500,"Internal Server Error");
    //No need to continue
    exit(0);
}

function set_error_header($code,$short){
    // All header cases for different servers (FAST CGI, Apache...)
    header($_SERVER["SERVER_PROTOCOL"]." ". $code ." ". $short);
    header("Status: ". $code ." ". $short);
    $_SERVER['REDIRECT_STATUS'] = $code;
}