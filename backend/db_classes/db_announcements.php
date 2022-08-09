<?php
    if(!defined("CONST_KEY") || CONST_KEY !== "035416f4-e65b-4fc6-a8db-301604ff31c5"){
        header("Location: ../404.html", true, 404);
        echo file_get_contents('../404.html');
	    die;
    }

    class db_announcements extends data_access{

        public function __construct(){
            $this->connect();
        }

        public function listAll(){
            $query = "SELECT `id`, `level`, `location`, `created_by` as createdBy, `created_date` as createdDate, `permanent`, `start_date` as startDate, `end_date` as endDate, `message` FROM `announcements` ORDER BY `location`, `level`";
            $statement = $this->pdo->prepare($query);
            $result = api_response::getResponse(404);
            try{
                $statement->execute();
                $announcements = $statement->fetchAll(PDO::FETCH_ASSOC);
                $result = api_response::getResponse(200);
                $result["messages"] = $announcements;
            }catch(Exception $e){
                log_util::logEntry("error", $e->getMessage());
                $result = api_response::getResponse(500);
                $result["exception"] = $e->getMessage();
            }finally{
                $this->disconnect();
            }
            return $result;
        }

        public function getByLocation($params){
            $result = api_response::getResponse(400);
            if(!isset($params['location'])){
                $result["exception"] = "No location provided.";
                return $result;
            }

            $query = "SELECT `id`, `level`, `location`, `created_by` as createdBy, `created_date` as createdDate, `permanent`, `start_date` as startDate, `end_date` as endDate, `message` FROM `announcements` WHERE `location` = :location ORDER BY `level`";
            $statement = $this->pdo->prepare($query);
            $result = api_response::getResponse(404);
            try{
                $statement->execute($params);
                $announcements = $statement->fetchAll(PDO::FETCH_ASSOC);
                $result = api_response::getResponse(200);
                $result["messages"] = $announcements;
            }catch(Exception $e){   
                log_util::logEntry("error", $e->getMessage());
                $result = api_response::getResponse(500);
                $result["exception"] = $e->getMessage();
            }finally{
                $this->disconnect();
            }
            return $result;
        }

        public function getById($params){
            $result = api_response::getResponse(400);
            if(!isset($params["id"])){
                $result["exception"] = "No id provided";
                return $result;
            }
            $query = "SELECT `id`, `level`, `location`, `created_by` as createdBy, `created_date` as createdDate, `permanent`, `start_date` as startDate, `end_date` as endDate, `message` FROM `announcements` WHERE `id` = :id";
            $statement = $this->pdo->prepare($query);
            $result = api_response::get(404);
            try{
                $statement->execute($params);
                $row = $statement->fetch(PDO::FETCH_ASSOC);
                if(is_array($row)){
                    $result = api_response::getResponse(200);
                    $result["message"] = $row;
                }
            }catch(Exception $e){
                log_util::logEntry("error", $e->getMessage());
                $result = api_response::getResponse(500);
                $result["exception"] = $e->getMessage();
            }finally{
                $this->disconnect();
            }
        }

        public function create($params){

        }

        public function update($params){

        }

        public function delete($params){

        }

    }

?>