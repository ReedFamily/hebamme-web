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

        public function getToken($params){
            $token = $this->tokenGen();
            $dbToken = new db_token();
            $response = array();
            try{
                $dbToken->persistToken($token);
            }catch(Exception $e){
                echo($e->getMessage());
                return api_response::getResponse(500);
            }
            
           $response = api_response::getResponse(200);
           $response["token"] = $token;
            
           return $response;
        }

        private function tokenGen(){
            return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',  mt_rand(0, 0xffff), mt_rand(0,0xffff), mt_rand(0,0xffff),(mt_rand(0,0x0fff) | 0x4000), (mt_rand(0, 0x3fff) | 0x8000), mt_rand(0, 0xffff),mt_rand(0, 0xffff),mt_rand(0, 0xffff));
        }
    }
?>