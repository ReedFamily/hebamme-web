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

    class announcements_processor
    {
        private $validLocations = array("home","kurse");
        private $levels = array("blue"=>"info", "yellow"=>"warning", "red"=>"danger");

        public function createAnnouncement($params){
            $result = api_response::getResponse(400);
            if(!isset($params["level"])){
                $result["exception"] = "No level is provided";
                return $result;
            }elseif(!isset($params["location"])){
                $result["exception"] = "No location is provided";
                return $result;
            }elseif(!isset($params["createdBy"])){
                $result["exception"] = "No created_by is provided";
                return $result;
            }elseif(!isset($params["createdDate"])){
                $result["exception"] = "No created_date is provided";
                return $result;
            }elseif(!isset($params["message"])){
                $result["exception"] = "No message is provided";
                return $result;
            }
            
            $values["level"] = $params["level"];
            $values["location"] = $params["location"];
            $values["createdBy"] = $params["createdBy"];
            
            $values["createdDate"] = $params["createdDate"];
            $values["message"] = $params["message"];
            if(!isset($params["permanent"])){
                $values["permanent"] = 0;
            }else{
                $values["permanent"] = $params["permanent"];
            }
            
            $db_msg = new db_announcements();   
            $result = $db_msg->create($values);
            if($result["status"] == 200){
                return $this->listAllAnnouncements();
            }
           return $result;
        }

        public function deleteAnnouncement($params){
            $db_msg = new db_announcements();
            $result = $db_msg->delete($params);
             if($result["status"] == 200){
                return $this->listAllAnnouncements();
            }
           return $result;
        }

        public function updateAnnouncement($params){
           
             if(isset($params["id"])){
                $values["id"] = $params["id"];
            }
            if(isset($params["level"])){
                $values["level"] = $params["level"];
            }
            if(isset($params["location"])){
                $values["location"] = $params["location"];
            }
            if(isset($params["createdBy"])){
                $values["createdBy"] = $params["createdBy"];
            }
            if(isset($params["createdDate"])){
                $values["createdDate"] = $params["createdDate"];
            }
            if(isset($params["permanent"])){
                $values["permanent"] = $params["permanent"];
            }
            if(isset($params["startDate"])){
                $values["startDate"] = $params["startDate"];
            }
            if(isset($params["endDate"])){
                $values["endDate"] = $params["endDate"];
            }
            if(isset($params["message"])){
                $values["message"] = $params["message"];
            }
            $db_msg = new db_announcements();
            $result = $db_msg->update($values);
           
            if($result["status"] == 200){
                return $this->listAllAnnouncements();
            }
           return $result;
        }

        public function listAnnouncementLocations(){
            $result = api_response::getResponse(200);
            $result["locations"] = $this->validLocations;
            return $result;
        }

        public function listAnnouncementLevels(){
            $result = api_response::getResponse(200);
            $result["levels"] = $this->levels;
            return $result;
        }

        public function listAllAnnouncements(){
            $db_msg = new db_announcements();
            $result = $db_msg->listAll();
            return $result;
        }

        public function getByAnnouncementById($params){
            $result = api_response::getResponse(400);
            if(!isset($params["id"])){
                $result["exception"] = "No id provided";
                return $result;
            }
            $values["id"] = $params["id"];
            $db_msg = new db_announcements();
            $result = $db_msg->getById($values);
            
            return $result;
        }

        public function getAnnouncementsByLocation($params){
            $db_msg = new db_announcements();
            $result = $db_msg->getByLocation($params);
            
            return $result;
        }

    }

?>