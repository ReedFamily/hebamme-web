<?php
    if(!defined("CONST_KEY") || CONST_KEY !== "035416f4-e65b-4fc6-a8db-301604ff31c5"){
        header("Location: ../404.html", true, 404);
        echo file_get_contents('../404.html');
        die;
    }

    class db_user extends data_access{

        public function __construct(){
            $this->connect();
        }

        public function getUserById($userId){
            $query = "SELECT `id`, `username`, `password`, `role` FROM `api_user` WHERE `id` = :userid";
            $params = ["userid" => $userId];
            $statement = $this->pdo->prepare($query);
            $result = api_response::getResponse(404);
            try{
                $statement->execute($params);
                $row = $statement->fetch(PDO::FETCH_ASSOC);
                if(is_array($row)){
                    $result = api_response::getResponse(200);
                    $result["user"] = $row;
                }
            }catch(Exception $e){
                $result = api_response::getResponse(500);
                $result["exception"] = $e->getMessage();
            }finally{
                $this->disconnect();
            }

            return $result;

        }

        public function getUserByName($userName){
            $query = "SELECT `id`, `username`, `password`, `role` FROM `api_user` WHERE `username` = :username";
            $params = ["username" => $userName];
            $statement = $this->pdo->prepare($query);
            $result = api_response::getResponse(403);
            try{
                $statement->execute($params);
                $row = $statement->fetch(PDO::FETCH_ASSOC);
                if(is_array($row)){
                    $result = api_response::getResponse(200);
                    $result["user"] = $row;
                }
            }catch(Exception $e){
                $result = api_response::getResponse(500);
                $result["exception"] = $e->getMessage();
            }finally{
                $this->disconnect();
            }

            if($result["status"] == 403){
                $result["message"] = "Invalid login.";
            }
            return $result;

        }

        public function listUsers(){
            $query = "SELECT `id`, `username`, `password`, `role` FROM `api_user`";
            $statement = $this->pdo->prepare($query);
            $result = api_response::getResponse(404);
            try{
                $statement->execute();
                $users = $statement->fetchAll(PDO::FETCH_ASSOC);
                $result = api_response::getResponse(200);
                $result["users"] = $users;
            }catch(Exception $e){
                $result = api_response::getResponse(500);
                $result["exception"] = $e->getMessage();
            }finally{
                $this->disconnect();
            }
            return $result;
        }

        public function createUser($user){
            $query = "INSERT INTO `api_user` (`username`, `password`, `role`) VALUES (:username, :password, :role)";
            $statement = $this->pdo->prepare($query);
            $result = api_response::getResponse(500);
            try{
                $this->pdo->beginTransaction();
                $statement->execute($user);
                $this->pdo->commit();
                $result = api_response::getResponse(200);
                $result["message"] = "User " . $user["username"] . " created.";
            }catch(Exception $e){
                $this->pdo->rollback();
                $result["exception"] = $e->getMessage();
            }finally{
                $this->disconnect();
            }
            return $result;
        }

        public function updateUser($user){
            
        }

        public function deleteUserById($userId){

        }

    }    

?>