<?php
     /*
        Gallery DAO
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

    class db_gallery extends data_access{

        public function __construct(){
            $this->connect();
        }

        public function listAllGalleries(){
            $query = "SELECT id, name, description, active FROM gallery";
            $statement = $this->pdo->prepare($query);
            $result = api_response::getResponse(404);
            try{
                $statement->execute();
                $galleries = $statement->fetchAll(PDO::FETCH_ASSOC);
                $result = api_response::getResponse(200);
                $result["galleries"] = $galleries;
            }catch(Exception $e){
                log_util::logEntry("error", $e->getMessage());
                $result = api_response::getResponse(500);
                $result["exception"] = $e->getMessage();
            }finally{
                $this->disconnect();
            }
            return $result;
        }

        public function getGalleryById($params){
            $query = "SELECT id as gallery_id, name as gallery_name, description, active FROM gallery WHERE id = :gallery_id";
            $statement = $this->pdo->prepare($query);
            try{
                $statement->execute($params);
                $gallery = $statement->fetch(PDO::FETCH_ASSOC);
                $result = api_response::getResponse(200);
                $result["gallery"] = $gallery;
            }catch(Exception $e){
                log_util::logEntry("error", $e->getMessage());
                $result = api_response::getResponse(500);
                $result["exception"] = $e->getMessage();
            }finally{
                $this->disconnect();
            }
            return $result;
        }

        public function getActiveGallery(){
            $query = "SELECT g.id as gallery_id, g.name as gallery_name, g.description as gallery_description, img.id as image_id, img.image_url as image_url, img.description as image_alt, img.height as height, img.width as width FROM gallery g JOIN gallery_images gi ON g.id = gi.gallery_id RIGHT JOIN images img ON gi.images_id = img.id WHERE g.active = 1";
            $statement = $this->pdo->prepare($query);
            $result = api_response::getResponse(404);
            try{
                $statement->execute();
                $galleryimages = $statement->fetchAll(PDO::FETCH_ASSOC);
                $result = api_response::getResponse(200);
                $result["galleryimages"] = $galleryimages;
            }catch(Exception $e){
                log_util::logEntry("error", $e->getMessage());
                $result = api_response::getResponse(500);
                $result["exception"] = $e->getMessage();
            }finally{
                $this->disconnect();
            }
            return $result;
        }

        public function setActiveGallery($params){
            $localParams["gallery_id"] = $params["gallery_id"];
            $clearQuery = "UPDATE gallery SET active = 0";
            $clearStmt = $this->pdo->prepare($clearQuery);
            $setQuery = "UPDATE gallery SET active = 1 WHERE id = :gallery_id";
            $setStmt = $this->pdo->prepare($setQuery);
            try{
                $clearStmt->execute();
                $setStmt->execute($localParams);
                $result = api_response::getResponse(200);
                $result["params"] = $params;
            }catch(Exception $e){
                log_util::logEntry("error", $e->getMessage());
                $result = api_response::getResponse(500);
                $result["exception"] = $e->getMessage();
                $result["params"] = $params;
            }
            finally{
                $this->disconnect();
            }
            return $result;
        }

        public function listAllImages($params){
            $query = "SELECT id, filename, image_url, description,height, width FROM images";
            $statement = $this->pdo->prepare($query);
            try{
                $statement->execute();
                $values = $statement->fetchAll(PDO::FETCH_ASSOC);
                $result = api_response::getResponse(200);
                $result["images"] = $values;
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

        public function registerUploadedImage($params){
            $query = "INSERT INTO images (`filename`, `image_url`, `height`, `width`, `description`) VALUES (:name, :image_url, :height, :width, :alt)";
            $statement = $this->pdo->prepare($query);
            try{
                $statement->execute($params);
                $result = api_response::getResponse(200);
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

        public function createGallery($params){
            $query = "INSERT INTO gallery (`name`, `description`, `active`) VALUES (:name, :description, 0)";
            $statement = $this->pdo->prepare($query);
            try{
                $statement->execute($params);
                $result = api_response::getResponse(200);
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

        public function modifyGallery($params){
            $values = array();
            if(isset($param["name"])){
                $values[] = "`name` = :name";
            }
            if(isset($param["description"])){
                $values[] = "`description` = :description";
            }
            if(count($values) == 0){
                $result = api_response::getResponse(200);
                return $result;
            }
            $query = "UPDATE gallery SET " . implode(",", $values) . " WHERE id = :id";
            $statement = $this->pdo->prepare($query);
            try{
                $statement->execute($params);
                $result = api_response::getResponse(200);
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

        public function addImageToGallery($params){
            $query = "INSERT INTO gallery_images (`gallery_id`, `images_id`) VALUES (:gallery_id, :images_id)";
            $statement = $this->pdo->prepare($query);
            try{
                $statement->execute($params);
                $result = api_response::getResponse(200);
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

        public function listImagesInGallery($params){
            $query = "SELECT gi.gallery_id as gallery_id, img.id as image_id, img.image_url as image_url, img.description as image_alt FROM gallery_images gi JOIN images img ON img.id = gi.images_id WHERE gi.gallery_id = :id";
            $statement = $this->pdo->prepare($query);
            try{
                $statement->execute($params);
                $result = api_response::getResponse(200);
                $galleryimages = $statement->fetchAll(PDO::FETCH_ASSOC);
                $result["images"] = $galleryimages;
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

        public function removeImageFromGallery($params){
            $query = "DELETE FROM gallery_images WHERE gallery_id = :gallery_id AND images_id = :images_id";
            $statement = $this->pdo->prepare($query);
            try{
                $statement->execute($params);
                $result = api_response::getResponse(200);
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

        public function deleteGallery($params){
            
            
            try{
                $this->clearGalleryForDeletion($params);
                $removeGallery = "DELETE FROM gallery WHERE id = :gallery_id";
                $rgStmt = $this->pdo->prepare($removeGallery);
               
                $rgStmt->execute($params);
                $result = api_response::getResponse(200);
            }catch(Exception $e){
                log_util::logEntry("error",$e->getMessage() . " " . $e->getLine() . " " . $e->getTraceAsString());
                $result = api_response::getResponse(500);
                $result["exception"] = $e->getMessage();
                $result["params"] = $params;
            }
            finally{
                $this->disconnect();
            }
            return $result;
        }

        private function clearGalleryForDeletion($params){
            if($this->doesGalleryHaveImages($params)){
                $removeImagesQuery = "DELETE FROM gallery_images WHERE gallery_id = :gallery_id";
                $riStmt = $this->pdo->prepare($removeImagesQuery);
                $riStmt->execute($params);
            }
        }

        private function doesGalleryHaveImages($params){

            $query = "SELECT COUNT(*) as cnt FROM gallery_images WHERE gallery_id = :gallery_id";
            $stmt = $this->pdo->prepare($query);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if(isset($res["cnt"]) && $res["cnt"] > 0){
                return true;
            }
            return false;
        }

        public function removeOrphanedImages($params){
            
        }

    }


?>