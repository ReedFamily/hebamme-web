<?php
    /*
        Centralized Logging Utility
        Copyright (c) 2021 - @COPYRIGHT_YEAR@ Jason Reed
        
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