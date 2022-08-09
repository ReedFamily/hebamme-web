<?php
    if(!defined("CONST_KEY") || CONST_KEY !== "035416f4-e65b-4fc6-a8db-301604ff31c5"){
        header("Location: ../404.html", true, 404);
        echo file_get_contents('../404.html');
	    die;
    }

    class announcements_processor
    {
        public function createAnnouncement($params){

        }

        public function deleteAnnouncement($params){

        }

        public function updateAnnouncement($params){

        }

        public function listAnnouncementLocations(){

        }

        public function listAllAnnouncements(){

        }

        public function getByAnnouncementById($params){
            
        }

        public function getAnnouncementsByLocation($params){

        }

    }

?>