<?php
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

    }
?>