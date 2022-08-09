<?php
    if(!defined("CONST_KEY") || CONST_KEY !== "035416f4-e65b-4fc6-a8db-301604ff31c5"){
        header("Location: ../404.html", true, 404);
        echo file_get_contents('../404.html');
        die;
    }

    abstract class data_access
    {
        protected $pdo;
        protected function connect(){
            $dns = "mysql:host=" . CONST_DB_SERV . ";dbname=" . CONST_DB_NAME;
            try{
                $this->pdo = new PDO($dns, CONST_DB_USER, CONST_DB_PASS);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }catch(PDOException $e){
                log_util::logEntry("error", $e->getMessage());
                echo "ERROR!: " . $e->getMessage() . "<br>";
                die();
            }
        }

        protected function prepareStatement($query){
            return $this->pdo->prepare($query);
        }

        protected function getPdo(){
            return $this->pdo;
        }

        protected function disconnect(){
            unset($this->pdo);
        }

    }

?>