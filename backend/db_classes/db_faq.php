<?php
    if(!defined("CONST_KEY") || CONST_KEY !== "035416f4-e65b-4fc6-a8db-301604ff31c5"){
        header("Location: ../404.html", true, 404);
        echo file_get_contents('../404.html');
        die;
    }

    class db_faq extends data_access
    {
        public function __construct(){
            $this->connect();
        }

        public function listFaqs(){
            $query = "SELECT `id`, `question`, `message` FROM faq";
            $stmt = $this->pdo->prepare($query);
            $result = api_response::getResponse(500);
            try{
                $stmt->execute();
                $faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $result = api_response::getResponse(200);
                $result["faqs"] = $faqs;
            }catch(Exception $e){
                log_util::logEntry("error", $e->getMessage());
                $result["exception"] = $e->getMessage();
            }finally{
                $this->disconnect();
            }
            return $result;
        }

        public function createFaq($faq){
            $query = "INSERT INTO `faq` (`question`, `message`) VALUES (:question, :message)";
            $result = api_response::getResponse(500);
            try{
                $stmt = $this->pdo->prepare($query);
                $this->pdo->beginTransaction();
                $stmt->execute($faq);
                $this->pdo->commit();
                $result = api_response::getResponse(200);
                $result["message"] = "FAQ Item Created";
            }catch(Exception $e){
                log_util::logEntry("error", $e->getMessage());
                $this->pdo->rollback();
                $result["exception"] = $e->getMessage();
            }finally{
                $this->disconnect();
            }
            return $result;
        }

        public function updateFaq($faq){
            if(!isset($faq["id"])){
                return $this->createFaq($faq);
            }

            $query = "UPDATE `faq` SET `question` = :question, `message` = :message WHERE id = :id";
            $result = api_response::getResponse(500);
            try{
                $stmt = $this->pdo->prepare($query);
                $this->pdo->beginTransaction();
                $stmt->execute($faq);
                $this->pdo->commit();
                $result = api_response::getResponse(200);
                $result["message"] = "FAQ id " . $faq["id"] . " updated";
            }catch(Exception $e){
                log_util::logEntry("error", $e->getMessage());
                $this->pdo->rollback();
                $result["exception"] = $e->getMessage();
            }finally{
                $this->disconnect();
            }
            return $result;

        }

        public function getFaq($id){
            $query = "SELECT `id`, `question`, message FROM `faq` WHERE `id` = :id";
            $params = ["id" => $id];
            $stmt = $this->pdo->prepare($query);
            $result = api_response::getResponse(404);
            try{
                $stmt->execute($params);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if(is_array($row)){
                    $result = api_response::getResponse(200);
                    $result["faq"] = $row;
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

        public function deleteFaq($id){
            $result = api_response::getResponse(500);
            try{
                $query = "DELETE FROM `faq` WHERE `id` = :id";
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