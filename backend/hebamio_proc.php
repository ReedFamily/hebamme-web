<?php
    if(!defined("CONST_KEY") || CONST_KEY !== "035416f4-e65b-4fc6-a8db-301604ff31c5"){
        header("Location: ../404.html", true, 404);
        echo file_get_contents('../404.html');
        die;
    }


    class hebamio_proc
    {
        
        private $keys = array("api_key" => CONST_HEBAMIO_KEY);

        public function requestHebamio(){
            if(!defined("CONST_HEBAMIO_KEY") || !defined("CONST_HEBAMIO_URL")){
                $response = api_response::getResponse(500);
                $response["exception"] = "HEBAMIO Access not configured.";
                return $response;
            }

            $out = $this->getClassList();
            $response = api_response::getResponse(200);
            $response["classes"] = $out;
            return $response;
        }

       

        private function getClassList(){
            
            $jsonOut = array();
            $url = CONST_HEBAMIO_URL . "/api/courses?" . http_build_query($this->keys);       
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $res = curl_exec($curl);
            curl_close($curl);
            $list = json_decode($res);
            foreach($list as $ndx=>$course){
                $cls["id"] = $course->id;
                $cls["name"] = $course->title;
                $detail = $this->getClassDetail($cls["id"]);

                if(isset($detail["status"])){
                    return $detail;
                }
               
                $cls["detail"] = $detail;

                $jsonOut[] = $cls;
            }
            return $jsonOut;
        }

        private function getClassDetail($classId){
            if(!is_numeric($classId)){
                $response = api_response::getResponse(400);
                $response["exception"] = "Course ID is not valid.";
                return $response;
            }
            $url = CONST_HEBAMIO_URL . "/api/course-detail/" . $classId . "?" . http_build_query($this->keys);

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $res = curl_exec($curl);
            curl_close($curl);
            
            $detail = json_decode($res, true);
            $clsData["hebamio_link"] = CONST_HEBAMIO_URL . "/anmeldung?reason=course-" . $classId;
            $clsData["date_start"] = $detail["date_start"];
            $clsData["date_end"] = $detail["date_end"];
            $clsData["price_partner"] = $detail["price_partner"];
            $clsData["price"] = $detail["price"];
            $clsData["instructor"] = $detail["instructor"];
            $clsData["max_paticipants"] = $detail["max_participants"];
            $clsData["available_space"] = $detail["available_space"];
            $clsData["location"]["address"] = $detail["location"]["adress"];
            $clsData["location"]["title"] = $detail["location"]["title"];
            $clsData["dates"] = $detail["dates"];
            return $clsData;
        }

    }

?>