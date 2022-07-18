<?php
    if(!defined("CONST_KEY") || CONST_KEY !== "035416f4-e65b-4fc6-a8db-301604ff31c5"){
        header("Location: ../404.html", true, 404);
        echo file_get_contents('../404.html');
        die;
    }

    class instructors_processor{
        public function createInstructor($params){
            if(!isset($params["post_body"])){
                return api_response::getResponse(400);
            }
            $instructor = $params["post_body"];
            try{
                $this->validateInstructorObject($instructor);
            }catch(InstructorException $e){
                $result = api_response::getResponse(400);
                $result["message"] = $e->__toString();
                log_util::logEntry("error", $e->__toString());
                return $result;
            }

            $db = new db_instructors();
            if(isset($instructor["id"])){
                unset($instructor["id"]);
            }
            $result = $db->createInstructor($instructor);
            if($result["status"] == 200){
                $result = $this->listInstructors();
            }
            return $result;

        }

        public function updateInstructor($params){
            if(!isset($params["post_body"])){
                return api_response::getResposne(400);
            }
            $instructor = $params["post_body"];
            if(!isset($instructor["id"]) || empty(trim($instructor["id"]))){
                return api_response::getResponse(400);
            }
            $db = new db_instructors();
            $result = $db->createInstructor($instructor);
            if($result["status"] == 200){
                $result = $this->listInstructors();
            }
            return $result;
        }

        public function listInstructors(){
            $db = new db_instructors();
            $result = $db->listAllInstructors();
            return $result;
        }

        public function getInstructor($params){
            if(!isset($params["id"]) || empty(trim($params["id"]))){
                return api_response::getResponse(400);
            }
            $db = new db_instructors();
            $result = $db->getInstructorById($params['id']);
            return $result;

        }

        public function deleteInstructor($params){
            if(!isset($params["id"]) || empty(trim($params["id"])) ){
                return api_response::getResponse(400);
            }
            $db = new db_instructors();
            $result = $db->deleteInstructorById($params["id"]);
            if($result["status"] == 200){
                $result = $this->listInstructors();
            }
            return $result;
        }

        private function validateInstructorObject($instructor){

            if(!isset($instructor["lastname"]) || empty(trim($instructor["lastname"]))){
                throw new InstructorException("lastname is empty");
            }
            if(!isset($instructor["firstname"]) || empty(trim($instructor["firstname"]))){
                throw new InstructorException("firstname is empty");
            }
            if(!isset($instructor["email"]) || empty(trim($instructor["email"]))){
                throw new InstructorException("email is empty");
            }
            if((!isset($instructor["phone"]) || empty(trim($instructor["phone"]))) && (!isset($instructor["mobile"]) || empty(trim($instructor["mobile"])))){
                throw new InstructorException("Phone Contact missing. Needs either phone or mobile");
            }
        }
    }
?>