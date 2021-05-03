<?php
    header('Access-Control-Allow-Origin: *'); 
    header('Access-Control-Allow-Methods: GET, POST');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Authorization');
    // Checks and defines constant to which prevents direct access.
    if(!defined("CONST_KEY")){define("CONST_KEY", "035416f4-e65b-4fc6-a8db-301604ff31c5");}

    $requestMethod = $_SERVER['REQUEST_METHOD'];
    require_once("./autoloader.php");

    if(in_array($requestMethod, ["GET", "POST"])){
        $requestMethodArray = array();
        $requestMethodArray = $_REQUEST;
        if(isset($requestMethodArray["apiFunc"])){ $functionName = $requestMethodArray["apiFunc"];}
        if(isset($requestMethodArray["apiParams"])){ $functionParams = $requestMethodArray["apiParams"];}


        echo(api_response::getResponse(200));
    }else{
        echo(api_response::getResponse(405));
    }


?>