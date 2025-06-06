<?php
    /*
        Instructors Processor
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
                $result = $this->listInstructors("");
            }
            return $result;

        }

        public function updateInstructor($params){
            if(!isset($params["post_body"])){
                return api_response::getResponse(400);
            }
            $instructor = $params["post_body"];
            if(!isset($instructor["id"]) || empty(trim($instructor["id"]))){
                return api_response::getResponse(400);
            }
            $db = new db_instructors();
            $result = $db->updateInstructor($instructor);
            if($result["status"] == 200){
                $result = $this->listInstructors("");
            }
            return $result;
        }

        public function listInstructors($params){
            $db = new db_instructors();
            if(isset($params) && isset($params["visible"]) && $params["visible"]=='true'){
                $result = $db->listAllVisibleInstructors();
            }else{
                $result = $db->listAllInstructors();
            }

            
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
                $result = $this->listInstructors("");
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