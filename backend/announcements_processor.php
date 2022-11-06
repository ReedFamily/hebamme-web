<?php
    if(!defined("CONST_KEY") || CONST_KEY !== "035416f4-e65b-4fc6-a8db-301604ff31c5"){
        header("Location: ../404.html", true, 404);
        echo file_get_contents('../404.html');
	    die;
    }

    class announcements_processor
    {
        private $validLocations = array("home");
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
            $db_msg = new db_announcements();
            $result = $db_msg->update($params);
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
            $db_msg = new db_announcements();
            $result = $db_msg->getById($params);
            if($result["status"] == 200){
                return $this->listAllAnnouncements();
            }
           return $result;
        }

        public function getAnnouncementsByLocation($params){
            $db_msg = new db_announcements();
            $result = $db_msg->getByLocation($params);
            if($result["status"] == 200){
                return $this->listAllAnnouncements();
            }
           return $result;
        }

    }

?>