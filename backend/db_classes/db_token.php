<?php
    if(!defined("CONST_KEY") || CONST_KEY !== "035416f4-e65b-4fc6-a8db-301604ff31c5"){
        header("Location: ../404.html", true, 404);
        echo file_get_contents('../404.html');
        die;
    }

    class db_token extends data_access
    {
        public function __construct(){
            $this->connect();
        }

        public function persistToken($token, $dateValue = null, $userId = null){
            $result = api_response::getResponse(200);
            if(!isset($dateValue)){
                $dateTime = new DateTime();
                $dateTime->add(new DateInterval("PT2M"));
                $dateValue = $dateTime->format("Y-m-d H:i:s");
            }
            if(!isset($userId)){
                $query = "INSERT INTO `api_tokens` (`token`, `valid_to`) VALUES (:token, :validTo)";
                $params = ["token" => $token, "validTo" => $dateValue];
            }else{
                $query = "INSERT INTO `api_tokens` (`token`, `valid_to`, `user_id`) VALUES (:token, :validTo, :userId)";
                $params = ["token" => $token, "validTo" => $dateValue, "userId" => $userId];
            }
            $stmt = $this->pdo->prepare($query);
            try{
                $this->pdo->beginTransaction();
                $stmt->execute($params);
                $this->pdo->commit();
            }catch(Exception $e){
                $this->pdo->rollback();
                $result = api_response::getResponse(500);
                $result["exception"] = $e->getMessage();
            }finally{
                $this->disconnect();
            }
            return $result;
        }

        public function clearOldTokens(){
            $response = api_response::getResponse(200);
            $dateTime = new DateTime();
            $query = "DELETE FROM `api_tokens` WHERE `valid_to` < :validTo";
            $dateValue = $dateTime->format("Y-m-d H:i:s");
            $params = ["validTo" => $dateValue];
            $stmt = $this->pdo->prepare($query);
            try{
                $this->pdo->beginTransaction();
                $stmt->execute($params);
                $this->pdo->commit();
            }catch(Exception $e){
                $this->pdo-rollback();
                $response = api_response::getResponse(500);
                $response["exception"] = $e->getMessage();
            }finally{
                $this->disconnect();
            }
            return $response;
        }

        public function isTokenValid($token){
            $result = api_response::getResponse(403);
            $dateTime = new DateTime();
            $query = "SELECT `token`, `valid_to` FROM `api_tokens` WHERE `token` = :token AND `valid_to` >= :validTo";
            $dateValue = $dateTime->format("Y-m-d H:i:s");
            $params = ["token" => $token ,"validTo" => $dateValue];
            $stmt = $this->pdo->prepare($query);
            try{
                $stmt->execute($params);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if(is_array($row)){
                    $dbToken = $row["token"];
                    if($token === $dbToken){
                        $result = api_response::getResponse(200);
                        $result["message"] = "Token OK";
                    }
                }
            }catch(Exception $e){
                $result = api_response::getResponse(500);
                $result["exception"] = $e->getMessage();
               
            }
            finally{
                $this->disconnect();
            }
            return $result;
        }

    }

?>