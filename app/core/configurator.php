<?php
/**
 * Configurator class
 * Loads config files: defaults, database configurations, and configurations for specific cores.
 *
 * @copyright (C) 2013 by OKFN Belgium
 * @license AGPLv3
 * @author Michiel Vancoillie
 * @author Pieter Colpaert
 */

if (!defined('T_ML_COMMENT')) {
	define('T_ML_COMMENT', T_COMMENT);
} else {
	define('T_DOC_COMMENT', T_ML_COMMENT);
}

class Configurator{

	/**
	 * Load config files and merge routes for cores
	 */
	public static function load($files){
		$config = array();


		foreach($files as $file){
			$content = file_get_contents(APPPATH. "config/". $file . ".json");
			$content = self::stripComments($content);
			$content = json_decode($content, TRUE);

			if($content == NULL){
				throw new ErrorException("Error: app/config/$file.json contains invalid JSON!");
			}

			if($file == "cores"){
				// Routes should be set alread, but to be safe
				if(empty($config['routes']))
					$config['routes'] = array();

				$extra_routes = array();

				if(empty($content))

				// Convert routes to the controllers with custom namespace for every core
				foreach($content as $core){
					if(!empty($core['routes'])){
						// Loop all routes for a specific core
						foreach($core['routes'] as $route => $controller){
							if(!empty($core['namespace']))
								$controller = $core['namespace']."\\".$controller;
							$extra_routes[$route] = $controller;
						}
					}
				}

				$config['routes'] = array_merge($config['routes'], $extra_routes);
			}else{
				$config[$file] = $content;
			}
		}

		return $config;
	}

	protected static function stripComments($content){
		$ret = preg_replace('/^(\s|\t)*\/\/.*$/m', "", $content);
		return trim($ret);
	}
}