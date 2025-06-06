<?php
     /*
        Instructors DAO
        Copyright (c) 2021 - @COPYRIGHT_YEAR@ Jason Reed
        
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

    class db_instructors extends data_access
    {
        public function __construct(){
             $this->connect();
        }

        public function listAllVisibleInstructors(){

            //  WHERE `viewable` = 1
            $query = "SELECT `id`, `last_name` as lastname, `first_name` as firstname, `email`, `phone`, `mobile`, `image_url` as imageurl, `description`, `position`, `registration_link` as hebamiolink, `team_member` as `team`, `viewable` as `visible` FROM `instructor` WHERE `viewable` = 1";
            $stmt = $this->prepareStatement($query);
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

        public function listAllInstructors(){
            $query = "SELECT `id`, `last_name` as lastname, `first_name` as firstname, `email`, `phone`, `mobile`, `image_url` as imageurl, `description`, `position`, `registration_link` as hebamiolink, `team_member` as `team`, `viewable` as `visible` FROM `instructor`";
            $stmt = $this->prepareStatement($query);
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
            if(isset($instructor["hebamiolink"])){
                $colnames .= ", `registration_link`";
                $paramNames .= ", :hebamiolink";
            }
            if(isset($instructor["team"])){
                $colnames .= ", `team_member`";
                $paramNames .= ", :team";
            }else{
                $colnames .= ", `team_member`";
                $paramNames .= ", 0";
            }
            if(isset($instructor["visible"])){
                $colnames .= ", `viewable`";
                $paramNames .= ", :visible";
            }else{
                $colnames .= ", `viewable`";
                $paramNames .= ", 1";
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
            $query = "SELECT `id`, `last_name` as lastname, `first_name` as firstname, `email`, `phone`, `mobile`, `image_url` as imageurl, `description`, `position`, `registration_link` as hebamiolink, `team_member` as team, `viewable` as visible FROM `instructor` WHERE `id` = :id";
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
            $setValues = "`last_name` = :lastname, `first_name` = :firstname";
            if(isset($instructor["email"])){
                $setValues .= ", `email` = :email";
            }
            if(isset($instructor["phone"])){
                $setValues .= ", `phone` = :phone";
            }
            if(isset($instructor["mobile"])){
                $setValues .= ", `mobile` = :mobile";
            }
            if(isset($instructor["imageurl"])){
                $setValues .=", `image_url` = :imageurl";
            }
            if(isset($instructor["description"])){
                $setValues .= ", `description` = :description";
            }else{
                log_util::logEntry("error", "No description found");
            }
            if(isset($instructor["position"])){
                $setValues .= ", `position` = :position";
            }
            if(isset($instructor["hebamiolink"])){
                $setValues .= ", `registration_link` = :hebamiolink";
            }
            if(isset($instructor["team"])){
                $setValues .= ", `team_member` = :team";
            }else{
                $setValues .= ", `team_member` = false";
            }
            if(isset($instructor["visible"])){
                $setValues .= ", `viewable` = :visible";
            }else{
                $setValues .= ", `viewable` = true";
            }


            $query = "UPDATE `instructor` SET $setValues WHERE `id` = :id";
            


            try{
                $stmt = $this->pdo->prepare($query);
                $result = api_response::getResponse(500);
                $this->pdo->beginTransaction();
                $stmt->execute($instructor);
                $this->pdo->commit();
                $result = api_response::getResponse(200);
                $result["message"] = "Instructor " . $instructor["firstname"] . " " . $instructor["lastname"] . " has been updated.";
            }catch(Exception $e){
                log_util::logEntry("error", $e->getMessage());
                log_util::logEntry("info", $query);
                $result = api_reponse::getReponse(500);
                $result["exception"] = $e->getMessage();
                $this->pdo->rollback();
            }finally{
                $this->disconnect();
            }

            return $result;
        }

        public function deleteInstructorById($id){
           $result = api_response::getResponse(500);
            try{
                $query = "DELETE FROM `instructor` WHERE `id` = :id";
                $params["id"] = $id;
               
                $stmt = $this->pdo->prepare($query);
                
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