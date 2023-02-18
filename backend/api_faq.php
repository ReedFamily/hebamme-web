<?php
    if(!defined("CONST_KEY") || CONST_KEY !== "035416f4-e65b-4fc6-a8db-301604ff31c5"){
        header("Location: ../404.html", true, 404);
        echo file_get_contents('../404.html');
        die;
    }

    class api_faq{
        public function listFaqs(){
            $db = new db_faq();
            $result = $db->listFaqs();
            return $result;
        }

        public function createFaq($params){
            if(!isset($params["post_body"])){
                return api_response::getResponse(400);
            }
            $faq = $params["post_body"];
            $db = new db_faq();
            if(isset($faq["id"])){
                unset($faq["id"]);
            }
            $result = $db->createFaq($faq);
            if($result["status"] == 200){
                $result = $this->listFaqs();
            }
            return $result;
        }

        public function getFaqById($params){
            if(!isset($params["id"]) || empty(trim($params["id"]))){
                return api_response::getResponse(400);
            }
            $db = new db_faq();
            $result = $db->getFaq($params["id"]);
            return $result;
        }

        public function changeFaq($params){
            if(!isset($params["post_body"])){
                return api_reponse::getResponse(400);
            }
            $faq = $params["post_body"];
            if(!isset($faq["id"]) || empty(trim($faq["id"]))){
                return api_reponse::getResponse(400);
            }
            $db = new db_faq();
            $result = $db->updateFaq($faq);
        }

        public function deleteFaqById($params){
            if(!isset($params["id"]) || empty(trim($params["id"]))){
                return api_response::getResponse(400);
            }

            $db = new db_faq();
            $result = $db->deleteInstructorById($params["id"]);
            if($result["status"] == 200){
                $result = $this->listFaqs();
            }
            return $result;
        }
    }
?>