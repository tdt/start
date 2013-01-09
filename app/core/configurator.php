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


class Configurator{

	/**
	 * Load config files and merge routes for cores
	 */
	public static function load($files){
		$config = array();

		foreach($files as $file){
			$content = file_get_contents($file);
			$content = json_decode($content, TRUE);

			if($file == "cores"){
				// Routes should be set alread, but to be safe
				if(empty($config['routes']))
					$config['routes'] = array();

				$extra_routes = array();

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
				$config[$file] = Configurator::load(APPPATH ."config/$file.json");
			}
		}

		return $config;
	}
}