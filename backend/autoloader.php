<?php
    if(!defined("CONST_KEY") || CONST_KEY !== "035416f4-e65b-4fc6-a8db-301604ff31c5"){
        header("Location: ../404.html", true, 404);
        echo file_get_contents('../404.html');
	    die;
    }
    require_once("conf/conf.php");

    $mapping = [
        "api_response" => "api_response.php",
        "EmailSender" => "email_processor.php",
        "ApiHandler" => "api_handler.php",
        "DataAccess" => "db_classes/data_access.php",
        "DbToken" => "db_classes/db_token.php"
    ];

    spl_autoload_register(function ($class) use ($mapping) {
        if (isset($mapping[$class])) {
            require_once $mapping[$class];
        }
    }, true);

?>