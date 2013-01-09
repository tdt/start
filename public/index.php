<?php
/**
 * @copyright (C) 2013 by OKFN Belgium
 * @license AGPLv3
 * @author Michiel Vancoillie
 * @author Pieter Colpaert
 */

// Set the environment for error reporting
define('ENVIRONMENT', 'production');

/**
 * Alright, here we go!
 *
 * -----------------
 * DANGER ZONE BELOW
 * -----------------
 */

if (defined('ENVIRONMENT'))
{
	switch (ENVIRONMENT)
	{
		case 'development':
			error_reporting(E_ALL);
		break;

		case 'testing':
		case 'production':
			error_reporting(0);
		break;

		default:
			exit('The application environment is not set correctly.');
	}
}


// Website document root
define('DOCROOT', __DIR__.DIRECTORY_SEPARATOR);

// Application directory
define('APPPATH', realpath(__DIR__.'/../app/').DIRECTORY_SEPARATOR);

// Vendor directory
define('VENDORPATH', realpath(__DIR__.'/../vendor/').DIRECTORY_SEPARATOR);

// Get the start time and memory
defined('DATATANK_START_TIME') or define('DATATANK_START_TIME', microtime(true));
defined('DATATANK_START_MEM') or define('DATATANK_START_MEM', memory_get_usage());

// Boot the datatank
require APPPATH.'bootstrap.php';