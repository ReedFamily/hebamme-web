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

?>