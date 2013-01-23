<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
/**
 * 	     Booting...
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

// Load the error and documentation controllers
require_once APPPATH . "controllers/ErrorController.class.php";
require_once APPPATH . "controllers/DocumentationController.class.php";

$c = new ErrorController();

// Keep cores as the last item
$config_files = array(
	"general",
	"routes",
	"db",
	"cores"
	);

// Check if all config files are present
foreach($config_files as $file){
	if(!file_exists(APPPATH. "config/". $file . ".json")){
		echo "The file $file doesn't exist. Please check whether you have copied ". APPPATH ."config/$file.example.json to ". APPPATH ."config/$file.json.";
		exit();
	}
}

// Start loading config files
try{
	$config = Configurator::load($config_files);
}catch(Exception $e){
	// TODO: show nice error page
	echo $e->getMessage();
	exit();
}

// Pass on the configuration
app\core\Config::setConfig($config);

// Start the router
require_once APPPATH."core/router.php";

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

// Initialize the timezone
date_default_timezone_set(app\core\Config::get("general","timezone"));

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