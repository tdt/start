<?php

class DocumentationController{
    public function GET($matches){
    	$location = app\core\Config::get("general", "hostname") . app\core\Config::get("general", "subdir");

		include(APPPATH . "template/header.php");
		include(APPPATH . "template/documentation.php");
		include(APPPATH . "template/footer.php");
    }
}