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
                    $res = $this->convertToWebP($targetFile);
                    unlink($targetFile);
                    // $res = api_response::getResponse(200);
                    // $res["imageurl"] = "backend/" . $targetFile;
                    // $res["message"] = "Upload Complete: " . $ok;
                    // $res["size"] = $check[3];
                    // $res["mime"] = $check["mime"];
                    return $res;
                }else{
                    $res = api_response::getResponse(302);
                    $res["imageurl"] = "backend/" . $targetFile;
                    $res["message"] = "Already Exists";
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


        private function convertToWebP($filepath){
            if(!file_exists($filepath)){
                $res = api_response::getResponse(400);
                $res["message"] = "{$fillepath} Not Found.";
                return $res;
            }
            $file_type = exif_imagetype($filepath);
            $newfilename = $filepath . ".webp";
            if(file_exists($newfilename)){
                $res = api_response::getResponse(200);
                $res["file"] = $newfilename;
                return $res;
            }
            if (function_exists('imagewebp')) {
                switch ($file_type) {
                    case '1': //IMAGETYPE_GIF
                        $image = imagecreatefromgif($filepath);
                        break;
                    case '2': //IMAGETYPE_JPEG
                        $image = imagecreatefromjpeg($filepath);
                        break;
                    case '3': //IMAGETYPE_PNG
                        $image = imagecreatefrompng($filepath);
                        imagepalettetotruecolor($image);
                        imagealphablending($image, true);
                        imagesavealpha($image, true);
                        break;
                    case '6': // IMAGETYPE_BMP
                        $image = imagecreatefrombmp($filepath);
                        break;
                    case '15': //IMAGETYPE_Webp
                        $res = api_response::getResponse(200);
                        $res["file"]=$filepath;
                        return $res;
                        break;
                    case '16': //IMAGETYPE_XBM
                        $image = imagecreatefromxbm($filepath);
                        break;
                    default:
                        $res = api_response::getResponse(400);
                        $res["message"] = "Unsupported File Type";
                        return $res;
                }
                $image = $this->resizeImage($image);
                $result = imagewebp($image, $newfilename, 90);
                if($result === false){

                    $res = api_response::getResponse(500);
                    $res["error"] = "Failed to convert image";
                    return $res;
                }
                $res = api_response::getResponse(200);
                $res["file"] = $newfilename;
                $res["size"] = imagesx($image) . "x" . imagesy($image);
                $res["mime"] = "image/webp";

                
                return $res;
            }else{
                $res = api_response::getResponse(500);
                $res["error"] = "No webp image support found";
                return $res;
            }
        }

        /** 
         * Simple resize function for image using the GDImage library of PHP. 
         * Original source image is destroyed and the resized image returned.
         */
        private function resizeImage($image){
            $maxsize = 1920;
            $oWidth = imagesx($image);
            $oHeight = imagesy($image);
            $ratio = $oWidth / $oHeight;
            $nWidth = 1920;
            $nHeight = 1920;
            if($ratio > 1){
                $nHeight = $maxsize / $ratio;
            }else{
                $nWidth = $maxsize * $ratio;
            }
            $resized = imagecreatetruecolor($nWidth, $nHeight);
            imagecopyresized($resized, $image, 0,0,0,0, $nWidth, $nHeight, $oWidth, $oHeight);
            return $resized;
        }

    }
?>