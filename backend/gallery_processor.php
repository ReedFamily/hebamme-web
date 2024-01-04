<?php
     /*
        Image Gallery Processor
        Copyright (c) 2021 - COPYRIGHT_YEAR Jason Reed
        
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
    class gallery_processor{

        public function listAllGalleries(){
            $db = new db_gallery();
            $result = $db->listAllGalleries();
            return $result;
        }

        public function getActiveGallery(){
            $db = new db_gallery();
            $dbresult = $db->getActiveGallery();
            if($dbresult["status"] == 200){
                $gallery["id"] = 0;
                $gallery["name"] = "";
                $gallery["description"] = "";
                $gallery["images"] = array();
                foreach($dbresult["galleryimages"] as $galleryImage){
                    if($gallery["id"] == 0){
                        $gallery["id"] = $galleryImage["gallery_id"];
                        $gallery["name"] = $galleryImage["gallery_name"];
                        $gallery["description"] = $galleryImage["gallery_description"];
                    }
                    $image["id"] = $galleryImage["image_id"];
                    $image["image_url"] = $galleryImage["image_url"];
                    $image["alt"] = $galleryImage["image_alt"];
                    array_push($gallery["images"], $image);
                }
                $result = api_response::getResponse(200);
                $result["gallery"] = $gallery;
            }else{
                $result = $dbresult;
            }

            return $result;
        }

        public function setActiveGallery($params){
            $db = new db_gallery();
            $result = $db->setActiveGallery($params);
            if($result["status"] == 200){
                $result = $this->listAllGalleries();
            }
            return $result;
        }

        public function createGallery($params){
            if(!isset($params["description"])){
                $params["description"] = "";
            }
            $db = new db_gallery();
            $result = $db->createGallery($params);
            if($result["status"] == 200){
                $result = $this->listAllGalleries();
            }
            return $result;
        }

        public function modifyGallery($params){
            $result = api_response::getResponse(400);
            if(!isset($params["id"]) || !is_numeric($params["id"])){
                $result["exception"] = "`id` parameter not provided.";
                return $result;
            }
            if(!isset($params["description"])){
                $params["description"] = "";
            }
            $db = new db_gallery();
            $result = $db->modifyGallery($params);
            if($result["status"] == 200){
                $result = $this->listAllGalleries();
            }
            return $result;
        }

        public function addImageToGallery($params){
            $result = api_response::getResponse(400);
            if(!isset($params["gallery_id"]) || !is_numeric($params["gallery_id"])){
                $result["exception"] = "`gallery_id` is not provided or is invalid";
                return $result;
            }
            if(!isset($params["images_id"]) || !is_numeric($params["images_id"])){
                $result["exception"] = "`images_id` is not provided or is invalid";
                return $result;
            }
            $db = new db_gallery();
            $result = $db->addImageToGallery($params);
            return $result;
        }

        public function listImagesInGallery($params){
             $result = api_response::getResponse(400);
            if(!isset($params["gallery_id"]) || !is_numeric($params["gallery_id"])){
                $result["exception"] = "`gallery_id` is not provided or is invalid";
                return $result;
            }
            $db = new db_gallery();
            $result = $db->listImagesInGallery($params);
            return $result;
        }

        public function removeImageFromGallery($params){
            $result = api_response::getResponse(400);
            if(!isset($params["gallery_id"]) || !is_numeric($params["gallery_id"])){
                $result["exception"] = "`gallery_id` is not provided or is invalid";
                return $result;
            }
            if(!isset($params["images_id"]) || !is_numeric($params["images_id"])){
                $result["exception"] = "`images_id` is not provided or is invalid";
                return $result;
            }
            $db = new db_gallery();
            $result = $db->removeImageFromGallery($params);
            return $result;
        }

        public function deleteGallery($params){
            $result = api_response::getResponse(400);
            if(!isset($params["id"]) || !is_numeric($params["id"])){
                $result["exception"] = "`id` parameter not provided.";
                return $result;
            }
            $db = new db_gallery();
            $result = $db->removeImageFromGallery($params);
            if($result["status"] == 200){
                $result = $this->listAllGalleries();
            }
            return $result;
        }

        public function removeOrphanedImages($params){

        }

    }
?>