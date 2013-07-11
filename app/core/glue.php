<?php

/**
 * Glue
 *
 * Provides an easy way to map URLs to classes. URLs can be literal
 * strings or regular expressions.
 *
 * When the URLs are processed:
 *      * delimiter (/) are automatically escaped: (\/)
 *      * The beginning and end are anchored (^ $)
 *      * An optional end slash is added (/?)
 *      * The i option is added for case-insensitive searches
 *
 * Example:
 *
 * $urls = array(
 *     '/' => 'index',
 *     '/page/(\d+) => 'page'
 * );
 *
 * class page {
 *      function GET($matches) {
 *          echo "Your requested page " . $matches[1];
 *      }
 * }
 *
 * Glue::stick($urls);
 *
 */
use app\core\Config;
use tdt\exceptions\TDTException;

class Glue {

    /**
     * stick
     *
     * The main static function of the glue class.
     *
     * @param   array    	$urls  	    The regex-based url to class mapping
     * @throws  Exception               Thrown if corresponding class is not found
     * @throws  Exception               Thrown if no match is found
     * @throws  BadMethodCallException  Thrown if a corresponding GET,POST is not found
     *
     */
    static function stick($urls) {
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        $HTTPheaders = getallheaders();
        if (isset($HTTPheaders["X-HTTP-Method-Override"])) {
            $method = strtoupper($HTTPheaders["X-HTTP-Method-Override"]);
        }

        // Drop first slash
        $path = preg_replace('/^\//', '', trim($_SERVER['REQUEST_URI']));

        //Compare two route objects (as defined in cores.json), based on their route regex, in reverse order.
        function routecompare($object1, $object2) {
            $compvalue = strcmp($object1['route'],$object2['route']);
            if ($compvalue == 0) {
                return 0;
            }
            else{
                //Reverse the order of the sort
                return -$compvalue;
            }
        }

        $found = false;

        usort($urls,"routecompare");
        
        // Logger configuration
        $exception_config = array();
        $exception_config["log_dir"] = Config::get("general", "logging", "path");
        $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";

        foreach ($urls as $url) {
            $classa = explode(".", $url['controller']);
            $class = $classa[0];

            // Check for a required user(s)
            $users = $url['users'];
            if (empty($users)){
                $users = null;
            }

            // Drop first slash of route
            $regex = preg_replace('/^\//', '', trim($url['route']));
            $regex = str_replace('/', '\/', $regex);
            $regex = '^' . $regex . '\/?$';

            if (preg_match("/$regex/i", $path, $matches)) {
                $found = true;
                if (class_exists($class)) {
                    if($users){
                        // Requires authentication
                        $match_autenticated = false;

                        $auth = null;
                        // Loop through allowed users, check if one is already authenticated
                        foreach($users as $user){
                            if($userconf = Config::get("auth", $user)){
                                $classname = "app\\auth\\" . $userconf['type'];

                                // Check if all users use the same authentication scheme
                                if($auth != null && !($auth instanceof $classname)){
                                    // Users need same auth scheme for one route
                                    // TODO: show better error here
                                    throw new TDTException(551, array($path, $method), $exception_config);
                                    break;
                                }else if($auth == null){
                                    $auth = new $classname();
                                }

                                // Check authentication for this user
                                if($auth->isAuthenticated($user)){
                                    $match_autenticated = true;
                                }
                            }
                        }

                        // None matched => authenticate
                        if(!$match_autenticated){
                            if($auth){
                                $auth->authenticate();
                            }else{
                                // Specified unexisting user as only authentication option
                                throw new TDTException(454, array($url['method'].' '.$url['route']) ,$exception_config);
                            }
                            exit();
                        }
                    }

                    $obj = new $class;
                    if (method_exists($obj, $method)) {
                        $obj->$method($matches);
                    } else {
                        throw new TDTException(450, array($path, $method), $exception_config);
                    }
                } else {
                    throw new TDTException(551, array($class),$exception_config);
                }
                break;
            }
        }
        if (!$found) {
            throw new TDTException(404, array($path),$exception_config);
        }
    }

}
