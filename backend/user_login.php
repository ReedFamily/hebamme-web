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


    class user_login
    {
        public function listUsers(){
            $dbUser = new db_user();
            return $dbUser->listUsers();
        }

        public function createUser($params){
            if(!isset($params["post_body"])){
                return api_response::getResponse(400);
            }
            $user = $params["post_body"];
            try{
                $this->validateUserObject($user);
            }catch(UserValidationException $e){
                $result = api_response::getResponse(400);
                $result["message"] = $e->__toString();
                log_util::logEntry("error", $e->__toString());
                return $result;
            }
            if(!isset($user["role"])){
                $user["role"] = 1;
            }
            $user["password"] = $this->hashPass($user["password"]);
            $dbUser = new db_user();
            $result = $dbUser->createUser($user);
            if($result["status"] == 200){
                $result = $this->listUsers();
            }
            return $result;
        }

        public function updateUser($params){
            if(!isset($params["post_body"])){
                return api_response::getResponse(400);
            }
            $user = $params["post_body"];
            if(!isset($user["id"]) || empty(trim($user["id"]))){
                return api_response::getResponse(400);
            }
            if(isset($user["password"])){
                $clearPass = $user["password"];
                $user["password"] = $this->hashPass($clearPass);
            }
            $dbUser = new db_user();
            $result = $dbUser->updateUser($user);
            if($result["status"] == 200){
                $result = $this->listUsers();
            }
            return $result;
        }

        public function deleteUser($params){
            if(!isset($params["userid"])){
                return api_response::getResponse(400);
            }
            $userId = $params["userid"];
            $dbUser = new db_user();
            $result = $dbUser->deleteUserById($userId);
            if($result["status"] == 200){
                $dbToken = new db_token();
                $result = $dbToken->deleteTokensByUserId($userId);
                 $result = $this->listUsers();
            }
            return $result;
        }

        public function loginUser($params){
            if(!isset($params["post_body"])){
                return api_response::getResponse(400);
            }
            $user = $params["post_body"];
            try{
                $this->validateUserObject($user);
            }catch(UserValidationException $e){
                $result = api_response::getResponse(400);
                $result["message"] = $e->__toString();
                log_util::logEntry("error", $e->__toString());
                return $result;
            }
            $dbResponse = $this->getUserFromDb($user["username"]);
            if($dbResponse["status"] != 200){
                return $dbResponse;
            }
            if(!isset($dbResponse["user"])){
                $result = api_response::getResponse(403);
                $result["message"] = "Invalid login.";
                log_util::logEntry("info", "Invalid login attempt {$user['username']}");
                return $result;
            }
            $dbUser = $dbResponse["user"];
            if(!password_verify($user["password"], $dbUser["password"])){
                $result = api_response::getResponse(403);
                $result["message"] = "Invalid login.";
                log_util::logEntry("info", "Invalid password for user {$user['username']}");
                return $result;
            }
            $apiToken = new api_token();
          
            return $apiToken->getLoginToken($dbUser["id"], $dbUser["username"]);
        }

        public function requestPasswordRecovery($params){
            $db = new db_user();
            if(!isset($params["username"])){
                return api_response::getResponse(400);
            }
            $userResponse = $db->findUserByName($params["username"]);
            if($userResponse["status"] != 200){
                return $userResponse;
            }
            $api_token = new api_token();
            $tokenResponse = $api_token->getRecoveryToken($userResponse["id"]);
            return $tokenResponse;


        }

        public function processPasswordRecovery($params){
            if(!isset($params["recoveryToken"])){
                return api_response::getResponse(400);
            }
            $token = $params["recoveryToken"];
        }

        public function getUserById($params){
            if(!isset($params["userid"])){
                return api_response::getResponse(400);
            }
            $dbUser = new db_user();
            $response = $dbUser->getUserById($params["userid"]);
            if($response["status"] == 200){
                unset($response["user"]["password"]);
            }
            return $response;
        }

        public function logoutUser($params){
            if(!isset($params["userid"])){
                return api_response::getResponse(400);
            }
            log_util::logEntry("debug","calling logout");
            $dbToken = new db_token();
            $userId = $params["userid"];
            $response = $dbToken->deleteTokensByUserId($userId);
            $response["message"] = "logged out";
            return $response;
        }

        private function getUserFromDb($username){
            $dbUser = new db_user();
            $result = $dbUser->getUserByName($username);
            return $result;
        }

        private function validateUserObject($user){
            
            if(!isset($user["username"]) || empty(trim($user["username"]))){
                throw new UserValidationException("The user name value is empty or missing.", 1000);
            }
            if(!isset($user["password"]) || empty(trim($user["password"]))){
                throw new UserValidationException("The password value is empty or missing.", 1001);
            }
        }

        private function hashPass($password){

            return password_hash($password, PASSWORD_DEFAULT);

        }




    }

?>