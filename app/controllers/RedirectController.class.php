<?php
/**
 * Implement 303: See other
 */
class RedirectController {
    function GET($matches){
        // get the current URL
        $location = app\core\Config::get("general", "hostname") . app\core\Config::get("general", "subdir");
        $pageURL = $location . $matches[0];
        $pageURL = rtrim($pageURL, "/");

        //add .about before the ?
        if (sizeof($_GET) > 0) {
            $pageURL = str_replace("?", ".about?", $pageURL);
            $pageURL = str_replace("/.about", ".about", $pageURL);
        } else {
            $pageURL .= ".about";
        }

        header("HTTP/1.1 303 See Other");
        header("Location:" . $pageURL);
    }
}