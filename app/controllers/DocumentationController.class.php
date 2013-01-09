<?php

class DocumentationController extends tdt\framework\AController{
    public function GET($matches){
    	$location = tdt\framework\Config::get("general", "hostname") . tdt\framework\Config::get("general", "subdir");

		include(APPPATH . "template/header.php");
		include(APPPATH . "template/documentation.php");
		include(APPPATH . "template/footer.php");
    }
}