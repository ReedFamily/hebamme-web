<?php
 if(!defined("CONST_KEY") || CONST_KEY !== "035416f4-e65b-4fc6-a8db-301604ff31c5"){
        header("Location: ../404.html", true, 404);
        echo file_get_contents('../404.html');
        die;
    }

    class log_util
    {
        public static function logEntry($level, $message){
            if(!defined("CONST_LOG_ENABLE") || CONST_LOG_ENABLE === false){
                return;
            }
            if(!defined("CONST_LOG_FILE")){
                return;
            }
            $dateTime = new DateTime();
            $dateValue = $dateTime->format("d.m.Y_H:i:s.u");
            if(!log_level::isValidLevel($level)){
                $level = "debug";
            }
            $level = strtolower($level);
            if(!self::isLogLevelEnabled($level)){
                return;
            }
            switch($level){
                case "info":
                    $formatLevel = log_level::INFO;
                    break;
                case "error":
                    $formatLevel = log_level::ERROR;
                    break;
                default:
                    $formatLevel = log_level::DEBUG;
                    break;
                
            }
            
            $logMsg = "{$dateValue}\t{$formatLevel}\t{$message}\n";
            error_log($logMsg, 3, CONST_LOG_FILE);

        }

        private static function isLogLevelEnabled($level){
            $result = true;
            if($level == 'debug' && (!defined("CONST_LOG_DEBUG") || CONST_LOG_DEBUG === false )){
                $result = false;
            }elseif($level == 'info' && (!defined("CONST_LOG_INFO") || CONST_LOG_INFO === false)){
                $result = false;
            }elseif($level == 'error' && (!defined("CONST_LOG_ERROR") || CONST_LOG_ERROR === false)){
                $result = false;
            }
            return $result;
        }

    }

?>