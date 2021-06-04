<?php
     if(!defined("CONST_KEY") || CONST_KEY !== "035416f4-e65b-4fc6-a8db-301604ff31c5"){
        header("Location: ../404.html", true, 404);
        echo file_get_contents('../404.html');
        die;
    }

    abstract class log_level
    {


        CONST DEBUG = "[DEBUG]";
        CONST INFO = "[INFO]";
        CONST ERROR = "[ERROR]";

        private static $constCacheArray = NULL;

        public static function isValidLevel($name){
            $consts = self::getConstants();
            $keys = array_map('strtolower', array_keys($consts));
            return in_array(strtolower($name), $keys);
        }

        private static function getConstants() {
            if (self::$constCacheArray == NULL) {
                self::$constCacheArray = [];
            }
            $calledClass = get_called_class();
            if (!array_key_exists($calledClass, self::$constCacheArray)) {
                $reflect = new ReflectionClass($calledClass);
                self::$constCacheArray[$calledClass] = $reflect->getConstants();
            }
            return self::$constCacheArray[$calledClass];
        }
    }

?>