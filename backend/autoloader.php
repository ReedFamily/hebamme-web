<?php
     /*
        This this class is used to consume the Hebamio API and provide a result of all classes and information in a single JSON structure.
        Copyright (c) 2021 - COPYRIGHT_YEAR Jason Reed
        
        Licensed under MIT License

        Permission is hereby granted, free of charge, to any person obtaining a copy
        of this software and associated documentation files (the "Software"), to deal
        in the Software without restriction, including without limitation the rights
        to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
        copies of the Software, and to permit persons to whom the Software is
        furnished to do so, subject to the following conditions:

        The above copyright notice and this permission notice shall be included in all
        copies or substantial portions of the Software.

        THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
        IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
        FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
        AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
        LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
        OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
        SOFTWARE.
    */
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
        "announcements_processor" => "announcements_processor.php",
        "user_login" => "user_login.php",
        "instructors_processor" => "instructors_processor.php",
        "upload_processor" => "upload_processor.php",
        "hebamio_proc" => "hebamio_proc.php",
        "api_faq" => "api_faq.php",
//      DB classes        
        "data_access" => "db_classes/data_access.php",
        "db_token" => "db_classes/db_token.php",
        "db_announcements" => "db_classes/db_announcements.php",
        "db_user" => "db_classes/db_user.php",
        "db_instructors" => "db_classes/db_instructors.php",
        "db_faq" => "db_classes/db_faq.php",
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