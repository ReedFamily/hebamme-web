<?php
     /*
        This this class is used to consume the Hebamio API and provide a result of all classes and information in a single JSON structure.
        Copyright (c) 2021 - COPYRIGHT_YEAR Jason Reed
        
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
            $qparam['location'] = $params['location'];
            $query = "SELECT `id`, `level`, `location`, `permanent`, `start_date` as `startDate`, `end_date` as `endDate`, `message` FROM `announcements` WHERE `location` = :location ORDER BY `level`";
            
            $result = api_response::getResponse(404);
            try{
                $statement = $this->pdo->prepare($query);
                $statement->execute($qparam);
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
           
            try{
                $query = "SELECT `id`, `level`, `location`, `created_by` as createdBy, `created_date` as createdDate, `permanent`, `start_date` as startDate, `end_date` as endDate, `message` FROM `announcements` WHERE `id` = :id";
                $statement = $this->pdo->prepare($query);
                $result = api_response::getResponse(404);
                $statement->execute($params);
                $row = $statement->fetch(PDO::FETCH_ASSOC);
                if(is_array($row)){
                    $result = api_response::getResponse(200);
                    $result["message"] = $row;
                }
                return $result;
            }catch(Exception $e){
                log_util::logEntry("error", $e->getMessage());
                $result = api_response::getResponse(500);
                $result["exception"] = $e->getMessage();
                return $result;
            }finally{
                $this->disconnect();
            }
        }

        public function create($params){
    
            try{
                $query = "INSERT INTO `announcements` (`level`, `location`, `created_by`, `created_date`, `permanent`,  `message`) VALUES (:level,:location,:createdBy,:createdDate,:permanent,:message)";
                $statement = $this->pdo->prepare($query);
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
            $query = "UPDATE `announcements` SET " . implode(",", $setValues) . " WHERE id = :id";

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
            $values["id"] = $params["id"];
            $query = "DELETE FROM `announcements` WHERE `id` = :id";
            try{
                $stmt = $this->prepareStatement($query);
                $this->pdo->beginTransaction();
                $stmt->execute($values);
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