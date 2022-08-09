<?php
    if(!defined("CONST_KEY") || CONST_KEY !== "035416f4-e65b-4fc6-a8db-301604ff31c5"){
        header("Location: ../404.html", true, 404);
        echo file_get_contents('../404.html');
	    die;
    }
    require_once("conf/conf.php");

    $mapping = [
//      Business classes
        "api_response" => "api_response.php",
        "email_sender" => "email_processor.php",
        "api_handler" => "api_handler.php",
        "api_token" => "api_token.php",
        "user_login" => "user_login.php",
        "instructors_processor" => "instructors_processor.php",
        "upload_processor" => "upload_processor.php",
//      DB classes        
        "data_access" => "db_classes/data_access.php",
        "db_token" => "db_classes/db_token.php",
        "db_user" => "db_classes/db_user.php",
        "db_instructors" => "db_classes/db_instructors.php",
//      Exception classes        
        "UserValidationException" => "exceptions/user_validation_exception.php",
        "InstructorException" => "exceptions/instructor_exception.php", 
        "log_util" => "logging/log_util.php",
        "log_level" => "logging/log_level.php"
    ];

    spl_autoload_register(function ($class) use ($mapping) {
        if (isset($mapping[$class])) {
            require_once $mapping[$class];
        }
    }, true);

?>