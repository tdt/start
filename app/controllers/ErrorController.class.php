<?php
/**
 * Show a decent error message
 */
class ErrorController {
    public function GET($matches){
        try{
            $errorcode = $matches[1];
            $problem = "";
            if(isset($_GET["problem"])){
                $problem = filter_var($_GET["problem"], FILTER_SANITIZE_STRING);
            }
            $title = "Error";

            include(APPPATH. "template/header.php");
            include(APPPATH. "template/error.php");
            include(APPPATH. "template/footer.php");
        }catch(Exception $e){
            echo "Error in the ErrorController: ". $e->getMessage();
            exit();
        }
    }
}
