<?php
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


require_once APPPATH. "core/glue.php";


// Support for CGI/FastCGI
if (!isset($_SERVER["REQUEST_URI"])){
	$_SERVER["REQUEST_URI"] = substr($_SERVER["PHP_SELF"], 1);
	if (isset($_SERVER["QUERY_STRING"])) {
		$_SERVER["REQUEST_URI"] .= "?".$_SERVER["QUERY_STRING"];
	}
}

// Fetch the routes from the config
$routes = tdt\framework\Config::get("routes");


// Only keep the routes that need the right HTTP message
$unsetkeys = preg_grep("/^" . strtoupper($_SERVER['REQUEST_METHOD']) . "/", array_keys($routes), PREG_GREP_INVERT);
foreach($unsetkeys as $key){
	unset($routes[$key]);
}

$log->logInfo("The routes we are working with", $routes);

try {
	// This function will do the magic.
	Glue::stick($routes);
}
catch(tdt\framework\TDTException $e){
	$log->logError($e->getMessage());
	set_error_header($e->getCode(),$e->getShort());
	echo "<script>location = \"" . $e->getURL() . "\";</script>";
}
catch(Exception $e){
	$log->logCrit($e->getMessage());
	setErrorHeader(500,"Internal Server Error");
	//add a javascript redirect to an error page
	echo "<script>location = \"" . tdt\framework\Config::get("general","hostname") . tdt\framework\Config::get("general","subdir") . "error/critical/\";</script>";
}