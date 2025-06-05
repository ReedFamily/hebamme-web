<?php
    /*
        FAQ Processor
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

    class api_faq{

        private $_padding = 60;

        public function __construct(){
            
            if(defined("CONST_FAQ_DATE_PADDING")){
                $this->_padding = CONST_FAQ_DATE_PADDING;
            }
        }
        public function listFaqs(){
            $db = new db_faq();
            $result = $db->listFaqs();
            if($result["status"] == 200){
                $len = sizeOf($result['faqs']);
                for($i = 0; $i < $len; $i++){
                    log_util::logEntry("INFO", "FAQ: " . $result['faqs'][$i]["id"]);
                    if($this->changedWithinRange($result['faqs'][$i]['changedAt'])){
                        if($this->isNew($result['faqs'][$i]['created'],$result['faqs'][$i]['changedAt'] )){
                            $result['faqs'][$i]['new'] = 1;
                        }elseif($this->isUpdated($result['faqs'][$i]['created'],$result['faqs'][$i]['changedAt'] )){
                            $result['faqs'][$i]['updated'] = 1;
                        }
                    }
                }
            }
            return $result;
        }

        private function changedWithinRange($changed){
            $currentDate = date_create();
            $localChanged = date_create($changed);
            log_util::logEntry("INFO", "changed: " . date_format($localChanged, "Y-m-d H:i:s") . " current: " . date_format($currentDate, "Y-m-d H:i:s"));
            $interval = date_diff($localChanged, $currentDate);
            $result = false;
            if($interval->format("%a") <= $this->_padding){
                log_util::logEntry("INFO", "Interval is {$interval->d} The padding is {$this->_padding}");
                $result = true;
            }
            return $result;
        }

        private function isNew($created, $changed){
            $result = false;
            $localCreated = date_create($created);
            $localChanged = date_create($changed);
            if($localCreated == $localChanged){
                $result = true;
            }
            return $result;
        }

        private function isUpdated($created, $changed){
            $result = false;
            $localCreated = date_create($created);
            $localChanged = date_create($changed);
            if($localCreated < $localChanged){
                $result = true;
            }
            return $result;
        }

        public function createFaq($params){
            if(!isset($params["post_body"])){
                $result = api_response::getResponse(400);
                $result["message"] = "No POST Body";
                return $result;
            }
            $faq = $params["post_body"];
            $db = new db_faq();
            if(isset($faq["id"])){
                unset($faq["id"]);
            }
            $result = $db->createFaq($faq);
            if($result["status"] == 200){
                $result = $this->listFaqs();
            }
            return $result;
        }

        public function getFaqById($params){
            if(!isset($params["id"]) || empty(trim($params["id"]))){
                return api_response::getResponse(400);
            }
            $db = new db_faq();
            $result = $db->getFaq($params["id"]);
            return $result;
        }

        public function changeFaq($params){
            if(!isset($params["post_body"])){
                return api_reponse::getResponse(400);
            }
            $faq = $params["post_body"];
            if(!isset($faq["id"]) || empty(trim($faq["id"]))){
                return api_reponse::getResponse(400);
            }
            $db = new db_faq();
            $result = $db->updateFaq($faq);
            if($result["status"] == 200){
                $result = $this->listFaqs();
            }
            return $result;
        }

        public function deleteFaqById($params){
            if(!isset($params["id"]) || empty(trim($params["id"]))){
                return api_response::getResponse(400);
            }

            $db = new db_faq();
            $result = $db->deleteFaq($params["id"]);
            if($result["status"] == 200){
                $result = $this->listFaqs();
            }
            return $result;
        }
    }
?>