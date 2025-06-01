<?php
     /*
        Base database access class
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

    abstract class data_access
    {
        protected $pdo;
        protected function connect(){
            $dns = "mysql:host=" . CONST_DB_SERV . ";dbname=" . CONST_DB_NAME .";charset=" . CONST_DB_CHAR;
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