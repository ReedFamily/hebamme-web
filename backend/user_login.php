<?php
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
                return $result;
            }
            if(!isset($user["role"])){
                $user["role"] = 0;
            }
            $user["password"] = $this->hashPass($user["password"]);
            $dbUser = new db_user();
            $result = $dbUser->createUser($user);
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
                return $result;
            }
            $dbResponse = $this->getUserFromDb($user["username"]);
            if($dbResponse["status"] != 200){
                return $dbResponse;
            }
            if(!isset($dbResponse["user"])){
                $result = api_response::getResponse(403);
                $result["message"] = "Invalid login.";
                return $result;
            }
            $dbUser = $dbResponse["user"];
            if(!password_verify($user["password"], $dbUser["password"])){
                $result = api_response::getResponse(403);
                $result["message"] = "Invalid login.";
                return $result;
            }
            // TODO add a token with longer validation time
            return api_response::getResponse(200);
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