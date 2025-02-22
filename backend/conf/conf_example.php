<?php
    if(!defined("CONST_KEY") || CONST_KEY !== "035416f4-e65b-4fc6-a8db-301604ff31c5"){
        header("Location: ../../404.html", true, 404);
        echo file_get_contents('../../404.html');
	    die;
    }
    /*
        This script is an example of the conf.php file that is needed for configuring the database connections.

    */

    define("CONST_DB_USER", ""); // Provide the database user name
    define("CONST_DB_PASS", ""); // Provide the database password
    define("CONST_DB_NAME", ""); // Provide the name of the database
    define("CONST_DB_SERV", ""); // Provide the database server location
    define("CONST_DB_CHAR", ""); // Provide the preferred character set ("utf8" is most preferred)
    define("CONST_SEND_TO", ""); // Address for delivery of contact form information
    define("CONST_LOG_ENABLE", false); // enables logging
    define("CONST_LOG_FILE", ""); // configures a logging file for debug.
    define("CONST_LOG_DEBUG", false); // enables debug logging
    define("CONST_LOG_INFO", false); // enables info logging
    define("CONST_LOG_ERROR", false); // enables error logging
    define("CONST_HEBAMIO_KEY", ""); // Configures the HEBAMIO API Key (provided by Hebamio)
    define("CONST_HEBAMIO_URL", ""); // Configures the base URL to HEBAMIO for the API without trailing /
    define("CONST_GALLERY_PATH", ""); // Configures the base directory for the gallery without trailing /

?>