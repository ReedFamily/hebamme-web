<?php
    /*
        This this class is used to consume the Hebamio API and provide a result of all classes and information in a single JSON structure.
        Copyright (c) 2021 - 2023 Jason Reed
        
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

    class api_token
    {
        public static function validate($apiToken, $adminToken){
            log_util::logEntry("debug", "Token $apiToken isAdmin $adminToken");
            api_token::cleanTokens();
            $cDbToken = new db_token();
            $response = $cDbToken->isTokenValid($apiToken);
            $response["func"] = "api_token::validate";
            if(!$adminToken){
                api_token::invalidateOneTimeToken($apiToken);
            }
            return $response;
        }

        public static function cleanTokens(){
            $cDbToken = new db_token();
            $cDbToken->clearOldTokens();
            log_util::logEntry("debug", "Old Tokens Cleared");
        }

        private static function invalidateOneTimeToken($token){
            $dbToken = new db_token();
            $response = $dbToken->deleteToken($token);
            if($response["status"] == 200){
                log_util::logEntry("debug", "Onetime Token Cleared");
            }else{
                log_util::logEntry("warn", "Onetime Token $token was not cleared");
            }
        }

        public function tokenValid($params){
            api_token::cleanTokens();
            $dbToken = new db_token();
            $response = array();
            if(!isset($params["token"]) || $params["token"] == ""){
                $response = api_response::getResponse(400);
                $response["message"] = "Token not present to validate";
                $response["params"] = $params;
                log_util::logEntry("error", "No Token to validate");
            }else{
                $response = $dbToken->isTokenValid($params["token"]);
                $response["token"] = $params["token"];
            }
            
            return $response;
        }

        public function getToken(){
            api_token::cleanTokens();
            $token = $this->tokenGen();
            $dbToken = new db_token();
            $response = array();
            try{
                $dbToken->persistToken($token);
            }catch(Exception $e){
                $response = api_response::getResponse(500);
                $response["exception"] =  $e->getMessage();
                log_util::logEntry("error", $e->getMessage());
                return $response;
            }
            
           $response = api_response::getResponse(200);
           $response["token"] = $token;
           return $response;
        }

        public function getResetToken($userId){
            $result = api_response::getResponse(500);
            $token = $this->tokenGen();
            $dateTime = new DateTime();
            $dateTime->add(new DateInterval("PT1H"));
            $dateValue = $dateTime->format("Y-m-d H:i:s");
            $dbToken = new db_token();
            try
            {
                $dbToken->persistToken($token, $dateValue, $userId);
            }catch(Exception $e){
                $response = api_response::getResponse(500);
                $response["exception"] = $e->getMessage();
                log_util::logEntry("error", $e->getMessage());
                return $response;
            }
            $response = api_response::getResponse(200);
            $response["token"] = $token;
            $response["validTo"] = $dateValue;
            $response["userId"] = $userId;
        }

        public function getLoginToken($userId, $username){
            $result = api_response::getResponse(500);
            $token = $this->tokenGen();
            $dateTime = new DateTime();
            $dateTime->add(new DateInterval("PT24H"));
            $dateValue = $dateTime->format("Y-m-d H:i:s");
            $dbToken = new db_token();
            try{
                $dbToken->persistToken($token, $dateValue, $userId);
            }catch(Exception $e){
                $response = api_response::getResponse(500);
                $response["exception"] = $e->getMessage();
                log_util::logEntry("error", $e->getMessage());
                return $response;
            }

            $response = api_response::getResponse(200);
            $response["token"] = $token;
            $response["validTo"] = $dateTime->format("r");
            $response["userId"] = $userId;
            $response["username"] = $username;
            return $response;
        }

        private function tokenGen(){
            return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',  mt_rand(0, 0xffff), mt_rand(0,0xffff), mt_rand(0,0xffff),(mt_rand(0,0x0fff) | 0x4000), (mt_rand(0, 0x3fff) | 0x8000), mt_rand(0, 0xffff),mt_rand(0, 0xffff),mt_rand(0, 0xffff));
        }

    }
?>