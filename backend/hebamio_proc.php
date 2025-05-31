<?php
    /*
        This this class is used to consume the Hebamio API and provide a result of all classes and information in a single JSON structure.
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

        private function getClassTypeFromTitle($title){
            if(str_contains($title, "Geburtsvorbereitung")){
                return "gbv";
            }
            if(str_contains($title, "Rückbildung")){
                return "rubi";
            }
            // Yoga is not currently available
            // if(str_contains($title, "Yoga")){
            //     return "yoga";
            // }
            if(str_contains($title, "Erste Hilfe")){
                return "eh";
            }
            if(str_contains($title, "Babytreff")){
                return "bt";
            }
            if(str_contains($title, "Babypflege")){
                return "bp";
            }
            if(str_contains($title, "Babymassage")){
                return "bm";
            }
            return "other";
        }

        private function sortClassList($list){
            $sortArray = array();
            foreach($list as $course){
                $sortArray[$course->id] = $course;
            }
            ksort($sortArray);
            return $sortArray;
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
            $sorted = $this->sortClassList($list);
            foreach($sorted as $ndx=>$course){
                $cls["id"] = $course->id;
                $cls["name"] = $course->title;
                $cls["type"] = $this->getClassTypeFromTitle($course->title);
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
            $clsData["date_start"] =  date("d.m.Y", strtotime($detail["date_start"]));
            $clsData["date_end"] = date("d.m.Y", strtotime( $detail["date_end"]));
            $clsData["price_partner"] = $detail["price_partner"];
            $clsData["price"] = $detail["price"];
            $clsData["instructor"] = $detail["instructor"];
            $clsData["max_paticipants"] = $detail["max_participants"];
            $clsData["available_space"] = $detail["available_space"];
            $clsData["location"]["address"] = $detail["location"]["adress"];
            $clsData["location"]["title"] = $detail["location"]["title"];
            
            foreach($detail["dates"] as $ndx=>$termin){
                $clsTerm["date"] = date("d.m.Y", strtotime($termin["date"]));
                $clsTerm["time_start"] = mb_substr($termin["time_start"], 0, 5);
                $clsTerm["time_end"] = mb_substr($termin["time_end"], 0, 5);
                $clsTerm["date_instructor"] = $termin["date_instructor"];
                $clsTerm["description"] = $termin["description"];
                $clsData["dates"][] = $clsTerm;
            }


            //$clsData["dates"] = $detail["dates"];
            


            return $clsData;
        }

    }

?>