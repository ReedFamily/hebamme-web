<?php
    if(!defined("CONST_KEY") || CONST_KEY !== "035416f4-e65b-4fc6-a8db-301604ff31c5"){
        header("Location: ../404.html", true, 404);
        echo file_get_contents('../404.html');
        die;
    }

    class api_token
    {
        public static function validate($apiToken){
            api_token::cleanTokens();
            $cDbToken = new db_token();
            return $cDbToken->isTokenValid($apiToken);
        }

        public static function cleanTokens(){
            $cDbToken = new db_token();
            $cDbToken->clearOldTokens();
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
                $response["message"] = "Creation of token failed " . $e->getMessage();
                return $response;
            }
            
           $response = api_response::getResponse(200);
           $response["token"] = $token;
            
           return $response;
        }

        public function getLoginToken($userId){
            $result = api_response::getResponse(500);
            $token = $this->tokenGen();
            $dateTime = new DateTime();
            $dateTime->add(new DateInterval("PT10H"));
            $dateValue = $dateTime->format("Y-m-d H:i:s");
            $dbToken = new db_token();
            try{
                $dbToken->persistToken($token, $dateValue, $userId);
            }catch(Exception $e){
                $response = api_response::getResponse(500);
                $response["message"] = "Creation of token failed " . $e->getMessage();
                return $response;
            }

            $response = api_response::getResponse(200);
            $response["token"] = $token;
            $response["validTo"] = $dateValue;
            return $response;
        }

        private function tokenGen(){
            return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',  mt_rand(0, 0xffff), mt_rand(0,0xffff), mt_rand(0,0xffff),(mt_rand(0,0x0fff) | 0x4000), (mt_rand(0, 0x3fff) | 0x8000), mt_rand(0, 0xffff),mt_rand(0, 0xffff),mt_rand(0, 0xffff));
        }
    }
?>