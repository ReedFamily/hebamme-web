<?php
    if(!defined("CONST_KEY") || CONST_KEY !== "035416f4-e65b-4fc6-a8db-301604ff31c5"){
        header("Location: ../404.html", true, 404);
        echo file_get_contents('../404.html');
	    die;
    }

    class announcements_processor
    {
        public function createAnnouncement($params){
            $db_msg = new db_announcements();
            $result = $db_msg->create($params);
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
            // TODO! Implementation
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