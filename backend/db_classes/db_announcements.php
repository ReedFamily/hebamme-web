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
            $result = api_response::getResponse(400);
            if(!isset($params["level"])){
                $result["exception"] = "No level is provided";
                return $result;
            }elseif(!isset($params["location"])){
                $result["exception"] = "No location is provided";
                return $result;
            }elseif(!isset($params["created_by"])){
                $result["exception"] = "No created_by is provided";
                return $result;
            }elseif(!isset($params["created_date"])){
                $result["exception"] = "No created_date is provided";
                return $result;
            }elseif(!isset($params["message"])){
                $result["exception"] = "No message is provided";
                return $result;
            }
            if(!isset($params["permanent"])){
                $params["permanent"] = 0;
            }
            if(!isset($params["start_date"])){
                $params["start_date"] = null;
            }
            if(!isset($params["end_date"])){
                $params["end_date"] = null;
            }
            $query = "INSERT INTO `announcements` (`level`, `location`, `created_by`, `created_date`, `permanent`, `start_date`, `end_date`, `message`) VALUES (:level, :location, :created_by, :created_date, :permanent, :start_date, :end_date, :message)";
            $statement = $this->prepareStatement($query);
            try{
                $this->pdo->beginTransaction();
                $statement->execute($params);
                $this->pdo->commit();
                $result = api_response::getResponse(200);
                $result["message"] = $params;
            }catch(Exception $e){
                log_util::logEntry("error", $e->getMessage());
                $this->pdo->rollback();
                $result["exception"]= $e->getMessage();
            }finally{
                $this->disconnect();
            }
            return $result;

        }

        public function update($params){
            $result = api_response::getResponse(400);
            if(!isset($params["id"])){
                $result["exception"] = "Id value not provided.";
                return $result;
            }
            $result = api_response::getResponse(500);
            $setValues = array();
            if(isset($params["level"])){
                $setValues[] = "`level` = :level";
            }
            if(isset($params["location"])){
                $setValues[] = "`location` = :location";
            }
            if(isset($params["createdBy"])){
                $setValues[] = "`created_by` = :createdBy";
            }
            if(isset($params["createdDate"])){
                $setValues[] = "`created_date` = :createdDate";
            }
            if(isset($params["permanent"])){
                $setValues[] = "`permanent` = :permanent";
            }
            if(isset($params["startDate"])){
                $setValues[] = "`start_date` = :startDate";
            }
            if(isset($params["endDate"])){
                $setValues[] = "`end_date` = :endDate";
            }
            if(isset($params["message"])){
                $setValues[] = "`message` = :message";
            }
            $query = "UPDATE `announcements` SET " + implode(",", $setValues) + " WHERE id = :id";

            try{
                $stmt = $this->prepareStatement($query);
                $this->pdo->beginTransaction();
                $stmt->execute($params);
                $this->pdo->commit();
                $result = api_response::getResponse(200);
                $result["message"] = "Announcement " . $params["id"] . " updated";
            }catch(Exception $e){
                log_util::logEntry("error", $e->getMessage());
                $result = api_response::getResponse(500);
                $result["exception"] = $e->getMessage();
                $this->pdo->rollback();
            }finally{
                $this->disconnect();
            }
            return $result;
        }

        public function delete($params){
            if(!isset($params["id"])){
                $result = api_response::getResponse(400);
                $result["exception"] = "The id value is missing";
                return $result;
            }

            $query = "DELETE FROM `announcements` WHERE `id` = :id";
            try{
                $stmt = $this->prepareStatement($query);
                $this->pdo->beginTransaction();
                $stmt->execute($params);
                $this->pdo->commit();
                $result = api_response::getResponse(200);
                $result["message"] = "Announcement with id " . $params["id"] . " deleted.";
                
            }catch(Exception $e){
                log_util::logEntry("error", $e->getMessage());
                $result = api_response::getResponse(500);
                $result["exception"] = $e->getMessage();
                $this->pdo->rollback();
            }finally{
                $this->disconnect();
            }

            return $result;

        }

    }

?>