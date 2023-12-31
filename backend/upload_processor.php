<?php
     /*
        Image upload manager
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

    class upload_processor{

        public function uploadAvatar($params){
            if(!isset($params["files"]["file"])){
                
                $res = api_response::getResponse(400);
                $res["message"] = "Can't find image in structure";
                return $res;
            }
           
            $targetDir = "uploads/";
            $targetFile = $targetDir . basename($params["files"]["file"]["name"]);
            $imageFileType = strtolower(pathinfo($targetFile,PATHINFO_EXTENSION));
            $check = getimagesize($params["files"]["file"]["tmp_name"]);
            if($check !== false){
                if(!$this->alreadyExists($targetFile)){
                    $ok = move_uploaded_file($params["files"]["file"]["tmp_name"], "./" . $targetFile);
                    $res = api_response::getResponse(200);
                    $res["imageurl"] = "backend/" . $targetFile;
                    $res["message"] = "Upload Complete: " . $ok;
                    return $res;
                }else{
                    $res = api_response::getResponse(200);
                    $res["imageurl"] = "backend/" . $targetFile;
                    $res["message"] = "Already Created";
                    return $res;
                }


                // $res = api_response::getResponse(200);
                // $res["message"] = "It's an image";
                // return $res;
            }else{
                $res = api_response::getResponse(400);
                $res["message"] = "Invalid image";
                return $res;
            }
        }

        private function alreadyExists($targetFile){
            if(file_exists($targetFile)){
                return true;
            }
            return false;
        }

        public function uploadGalleryPhoto($params){
            if(!isset($params["files"]["file"])){ 
                $res = api_response::getResponse(400);
                $res["message"] = "Can't find image in structure";
                return $res;
            }
            $targetDir = CONST_GALLERY_PATH;
            $targetFile = $targetDir . basename($params["files"]["file"]["name"]);
            $imageFileType = strtolower(pathinfo($targetFile,PATHINFO_EXTENSION));
            $check = getimagesize($params["files"]["file"]["tmp_name"]);
            if($check !== false){
                if(!$this->alreadyExists($targetFile)){
                    $ok = move_uploaded_file($params["files"]["file"]["tmp_name"], "./" . $targetFile);
                    $res = api_response::getResponse(200);
                    $res["imageurl"] = "backend/" . $targetFile;
                    $res["message"] = "Upload Complete: " . $ok;
                    $res["size"] = $check;
                    return $res;
                }else{
                    $res = api_response::getResponse(200);
                    $res["imageurl"] = "backend/" . $targetFile;
                    $res["message"] = "Already Created";
                    return $res;
                }


                // $res = api_response::getResponse(200);
                // $res["message"] = "It's an image";
                // return $res;
            }else{
                $res = api_response::getResponse(400);
                $res["message"] = "Invalid image";
                return $res;
            }
        }

    }
?>