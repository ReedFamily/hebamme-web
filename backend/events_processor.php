<?php
    if(!defined("CONST_KEY") || CONST_KEY !== "035416f4-e65b-4fc6-a8db-301604ff31c5"){
        header("Location: ../404.html", true, 404);
        echo file_get_contents('../404.html');
        die;
    }


    class events_processor{

        public function getEvents($params){

        }

        public function createEvent($params){

        }

        public function editEvent($params){

        }

        public function deleteEvent($params){
            
        }


    }


?>