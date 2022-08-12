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
            $query = "SELECT `id`, `username`,`first_name`, `last_name`, `password`, `email`, `role` FROM `api_user` WHERE `id` = :userid";
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
                log_util::logEntry("error", $e->getMessage());
                $result = api_response::getResponse(500);
                $result["exception"] = $e->getMessage();
            }finally{
                $this->disconnect();
            }

            return $result;

        }

        public function getUserByEmail($userEmail){
            $query = "SELECT `id`, `username`, `password`, `email`, `role` FROM `api_user` WHERE `email` = :email";
            $params = ["email" => $userEmail];
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
                log_util::logEntry("error", $e->getMessage());
                $result = api_response::getResponse(500);
                $result["exception"] = $e->getMessage();
            }finally{
                $this->disconnect();
            }
            return $result;
        }

        public function getUserByName($userName){
            $query = "SELECT `id`, `username`, `password`, `email`, `role` FROM `api_user` WHERE `username` = :username";
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
                log_util::logEntry("error", $e->getMessage());
                $result = api_response::getResponse(500);
                $result["exception"] = $e->getMessage();
            }finally{
                $this->disconnect();
            }

            if($result["status"] == 403){
                $result["message"] = "Invalid login.";
                log_util::logEntry("info", "Invalid login attempt {$userName}");
            }
            return $result;

        }

        public function listUsers(){
            $query = "SELECT `id`, `username`, `last_name`, `first_name`, `email`, `role` FROM `api_user` ORDER BY `id`";
            $statement = $this->pdo->prepare($query);
            $result = api_response::getResponse(404);
            try{
                $statement->execute();
                $users = $statement->fetchAll(PDO::FETCH_ASSOC);
                $result = api_response::getResponse(200);
                $result["users"] = $users;
            }catch(Exception $e){
                log_util::logEntry("error", $e->getMessage());
                $result = api_response::getResponse(500);
                $result["exception"] = $e->getMessage();
            }finally{
                $this->disconnect();
            }
            return $result;
        }

        public function createUser($user){
            log_util::logEntry("debug", json_encode($user));
            $query = "INSERT INTO `api_user` (`username`,`last_name`, `first_name`, `password`, `email` , `role`) VALUES (:username, :lastname, :firstname, :password, :email, :role)";
            log_util::logEntry("debug", $query);
            $statement = $this->pdo->prepare($query);
            $result = api_response::getResponse(500);
            try{
                $this->pdo->beginTransaction();
                $statement->execute($user);
                $this->pdo->commit();
                $result = api_response::getResponse(200);
                $result["message"] = "User " . $user["username"] . " created.";
            }catch(Exception $e){
                log_util::logEntry("error", $e->getMessage());
                $this->pdo->rollback();
                $result["exception"] = $e->getMessage();
            }finally{
                $this->disconnect();
            }
            return $result;
        }

        public function updateUser($user){
            $columns = "`username` = :username, `last_name` = :lastname, `first_name` = :firstname, `email` = :email";
            if(isset($user['password'])){
                $columns .= ", `password` = :password";
            }

            $query = "UPDATE `api_user` SET {$columns} WHERE `id` = :id";
            log_util::logEntry("debug", $query);
            $statement = $this->pdo->prepare($query);
            $result = api_response::getResponse(500);
            try{
                $this->pdo->beginTransaction();
                $statement->execute($user);
                $this->pdo->commit();
                $result = api_response::getResponse(200);
            }catch(Exception $e){
                log_util::logEntry("error", $e->getMessage());
                $this->pdo->rollback();
                $result["exception"] = $e->getMessage();
            }finally{
                $this->disconnect();
            }
            return $result;

        }

        public function deleteUserById($userId){
            $query = "DELETE FROM `api_user` WHERE `id` = :id";
            $statement = $this->pdo->prepare($query);
            $params = ["id" => $userId];
            try{
                $this->pdo->beginTransaction();
                $statement->execute($params);
                $this->pdo->commit();
                $result = api_response::getResponse(200);
            }catch(Exception $e){
                log_util::logEntry("error", $e->getMessage());
                $this->pdo->rollback();
                $result["exception"] = $e->getMessage();
            }finally{
                $this->disconnect();
            }
            return $result;
        }

    }    

?>