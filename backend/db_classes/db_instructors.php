<?php
    if(!defined("CONST_KEY") || CONST_KEY !== "035416f4-e65b-4fc6-a8db-301604ff31c5"){
        header("Location: ../404.html", true, 404);
        echo file_get_contents('../404.html');
        die;
    }

    class db_instructors extends data_access
    {
        public function __construct(){
             $this->connect();
        }

        public function listAllInstructors(){
            $query = "SELECT `id`, `last_name` as lastname, `first_name` as firstname, `email`, `phone`, `mobile`, `image_url` as imageurl, `description`, `position` FROM `instructor`";
            $stmt = $this->pdo->prepare($query);
            $result = api_response::getResponse(500);
            try{
                $stmt->execute();
                $instructors = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $result = api_response::getResponse(200);
                $result["instructors"] = $instructors;
            }catch(Exception $e){
                log_util::logEntry("error", $e->getMessage());
                $result["exception"] = $e->getMessage();
            }finally{
                $this->disconnect();
            }
            return $result;
        }

        public function createInstructor($instructor){

            $colnames = "`last_name`, `first_name`, `email`";
            $paramNames = ":lastname, :firstname, :email";
            
            if(isset($instructor["phone"])){
                $colnames .= ", `phone`";
                $paramNames .= ", :phone";
            }
            if(isset($instructor["mobile"])){
                $colnames .= ", `mobile`";
                $paramNames .= ", :mobile";
            }
            if(isset($instructor["imageurl"])){
                $colnames .= ", `image_url`";
                $paramNames .= ", :imageurl";
            }
            if(isset($instructor["description"])){
                $colnames .= ", `description`";
                $paramNames .= ", :description";
            }
            if(isset($instructor["position"])){
                $colnames .= ", `position`";
                $paramNames .= ", :position";
            }

            $query = "INSERT INTO `instructor` ($colnames) VALUES ($paramNames)";
            
            $result = api_response::getResponse(500);
            
           
           
            try{
                $stmt = $this->pdo->prepare($query);
                $this->pdo->beginTransaction();
                $stmt->execute($instructor);
                $this->pdo->commit();
                $result = api_response::getResponse(200);
                $result["message"] = "Instructor " . $instructor['firstname'] . " " . $instructor["lastname"] . " created.";
            }catch(Exception $e){
                log_util::logEntry("error", $e->getMessage());
                log_util::logEntry("error", $query);
                $this->pdo->rollback();
                $result["exception"] = $e->getMessage();
            }finally{
                $this->disconnect();
            }
            return $result;
        }

        public function getInstructorById($id){
            $query = "SELECT `id`, `last_name` as lastname, `first_name` as firstname, `email`, `phone`, `mobile`, `image_url` as imageurl, `description`, `position` FROM `instructor` WHERE `id` = :id";
            $params = ["id" => $id];
            $stmt = $this->pdo->prepare($query);
            $result = api_response::getResponse(404);
            try{
                $stmt->execute($params);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if(is_array($row)){
                    $result = api_response::getResponse(200);
                    $result["instructor"] = $row;
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

        public function updateInstructor($instructor){
            if(!isset($instructor["id"])){
                $result = api_response::getResponse(400);
                $result["message"] = "No id number assigned to Instructor. Create Instructor should be used.";
                return $result;
            }

            $query = "UPDATE `instructor` SET `last_name` = :lastname, `first_name` = :firstname, `email` = :email, `phone` = :phone, `mobile` = :mobile, `image_url` = :thumbnail, `description` = :descript, `position` = :position WHERE `id` = :id";
            $stmt = $this->pdo->prepare($query);
            $result = api_response::getResponse(500);
            try{
                $this->pdo->beginTransaction();
                $stmt->execute($instructor);
                $this->pdo->commit();
                $result = api_response::getResponse(200);
                $result["message"] = "Instructor " . $instructor["firstname"] . " " . $instructor["lastname"] . " has been updated.";
            }catch(Exception $e){
                log_util::logEntry("error", $e->getMessage());
                $result = api_reponse::getReponse(500);
                $result["exception"] = $e->getMessage();
                $this->pdo->rollback();
            }finally{
                $this->disconnect();
            }

            return $result;
        }

        public function deleteInstructorById($id){
            $query = "DELETE FROM `instructor` WHERE `id` = :id";
            $params = ["id" => $id];
            $stmt = $this->pdo->prepare($query);
            $result = api_response::getResponse(500);
            try{
                $this->pdo->beginTransaction();
                $stmt->execute($params);
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