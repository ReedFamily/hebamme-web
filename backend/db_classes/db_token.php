<?php
    if(!defined("CONST_KEY") || CONST_KEY !== "035416f4-e65b-4fc6-a8db-301604ff31c5"){
        header("Location: ../404.html", true, 404);
        echo file_get_contents('../404.html');
        die;
    }

    class DbToken extends DataAccess
    {
        public function __construct(){
            $this->connect();
        }

        public function persistToken($token){
            $dateTime = new DateTime();
            $dateTime->add(new DateInterval("PT10M"));
            $dateValue = $dateTime->format("Y-m-d H:i:s");
            $query = "INSERT INTO `api_tokens` (`token`, `valid_to`) VALUES (:token, :validTo);";
            $stmt = $this->pdo->prepare($query);
            $params = ["token" => $token, "validTo" => $dateValue];
            try{
                $this->pdo->beginTransaction();
                $stmt->execute($params);
                $this->pdo->commit();
            }catch(Exception $e){
                $this->pdo->rollback();
                throw $e;
            }finally{
                $this->disconnect();
            }
        }

        public function clearOldTokens(){
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
                throw $e;
            }finally{
                $this->disconnect();
            }
        }

        public function isTokenValid($token){
            $result = false;
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
                        $result = true;
                    }
                }
            }catch(Exception $e){
                throw $e;
            }
            finally{
                $this->disconnect();
            }
            return $result;
        }

    }

?>