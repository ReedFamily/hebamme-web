<?php
     /*
        Token DAO
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
                $query = "INSERT INTO `api_tokens` (`token`, `valid_to`, `ip`) VALUES (:token, :validTo, :ip)";
                $params = ["token" => $token, "validTo" => $dateValue, "ip"=>$_SERVER["REMOTE_ADDR"]];
            }else{
                $query = "INSERT INTO `api_tokens` (`token`, `valid_to`, `user_id`, `ip`) VALUES (:token, :validTo, :userId, :ip)";
                $params = ["token" => $token, "validTo" => $dateValue, "userId" => $userId, "ip" => $_SERVER["REMOTE_ADDR"]];
            }
            $stmt = $this->pdo->prepare($query);
            try{
                $this->pdo->beginTransaction();
                $stmt->execute($params);
                $this->pdo->commit();
            }catch(Exception $e){
                log_util::logEntry("error", $e->getMessage());
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
                log_util::logEntry("error", $e->getMessage());
                $this->pdo-rollback();
                $response = api_response::getResponse(500);
                $response["exception"] = $e->getMessage();
            }finally{
                $this->disconnect();
            }
            return $response;
        }

        public function isTokenValid($token, $userId = null){
            $result = api_response::getResponse(403);
            $dateTime = new DateTime();
            $dateValue = $dateTime->format("Y-m-d H:i:s");
            $query = "SELECT `token`, `valid_to`, `user_id`, `ip` FROM `api_tokens` WHERE `token` = :token AND `valid_to` >= :validTo";
            $params = ["token" => $token ,"validTo" => $dateValue];
            $stmt = $this->pdo->prepare($query);
            try{
                $stmt->execute($params);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if(is_array($row)){
                    $dbToken = $row["token"];
                    $tokenIp = $row["ip"];
                    $tokenUser = $row["user_id"];
                    if($token === $dbToken){
                        log_util::logEntry("debug", "$dbToken is valid");
                        $result = api_response::getResponse(200);
                        $result["message"] = "Token OK";
                        $result["user"] = $tokenUser;
                        $result["ip"] = $tokenIp;
                    }else{
                        log_util::logEntry("error", "Invalid token $dbToken expected $token");
                    }
                }
            }catch(Exception $e){
                log_util::logEntry("error", $e->getMessage());
                $result = api_response::getResponse(500);
                $result["exception"] = $e->getMessage();
               
            }
            finally{
                $this->disconnect();
            }
            return $result;
        }

        public function deleteToken($token){
            $result = api_response::getResponse(500);
            $query = "DELETE FROM `api_tokens` WHERE `token` = :token";
            $statement = $this->pdo->prepare($query);
            $params = ["token" => $token];
            try{
                $this->pdo->beginTransaction();
                $statement->execute($params);
                $this->pdo->commit();
                $result = api_response::getResponse(200);
            }catch(Exception $e){
                log_util::logEntry("error", $e->getMessage());
                $result["exception"] = $e->getMessage();
                $this->pdo->rollback();
            }finally{
                $this->disconnect();
            }
            return $result;
        }

        public function deleteTokensByUserId($id){
            $result = api_response::getResponse(500);
            $query = "DELETE FROM `api_tokens` WHERE `user_id` = :id";
            $statement = $this->pdo->prepare($query);
            $params = ["id" => $id];
            try{
                $this->pdo->beginTransaction();
                $statement->execute($params);
                $this->pdo->commit();
                $result = api_response::getResponse(200);
            }catch(Exception $e){
                log_util::logEntry("error", $e->getMessage());
                $result["exception"] = $e->getMessage();
                $this->pdo->rollback();
            }finally{
                $this->disconnect();
            }
            return $result;
        }

    }

?>