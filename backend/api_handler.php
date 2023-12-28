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
    require_once("email_processor.php");
    class api_handler
    {
        private $function_map;

        public function __construct(){
            $this->loadFunctionMap();
        }
        
        public function callApiFunction($apiFunction, $apiParams, $token, $adminToken){
            $res = $this->getCommand($apiFunction);
            if(is_null($token)){
                $token = "";
            }
            if($res['success'] === true){
                $needToken = $res["dataArray"]["needToken"];
                $class = $res["dataArray"]["class"];
                $func = $res["dataArray"]["function_name"];
               

                if($needToken == true){
                    if( $token == ""){
                        $res = api_response::getResponse(400);
                        $res["message"] = "apiToken failure likely in cookies.";
                        $res["cookie"] = $_COOKIE;
                        $res["request"] = $requestMethodArray;
                        $res["params"] = $functionParams;
                    }else{
                        $res = api_token::validate($token, $adminToken);
                    }
                }
                if($res["status"] !== 200){
                    return res;
                }

                $cCommand = new $class();
                $res = $cCommand->$func($apiParams);
            }
            return $res;
        }

        private function getCommand($apiFunction){
            if(isset($this->function_map[$apiFunction])){
                $res = api_response::getResponse(200);
                $res["dataArray"] = $this->function_map[$apiFunction];
                return $res;
            }else{
                $res = api_response::getResponse(405);
                return $res;
            }
        }

        private function loadFunctionMap(){
            $this->function_map = [ 
                'getToken'          =>  ['class'    =>  'api_token',                'function_name' => 'getToken', 'needToken' => false],
                'tokenValid'        =>  ['class'    =>  'api_token',                'function_name'=> 'tokenValid', 'needToken' => false],
                'sendContact'       =>  ['class'    =>  'email_sender',          'function_name' => 'send_contact', 'needToken' => false],
                'listUsers'         =>  ['class'    =>  'user_login',               'function_name' =>'listUsers', 'needToken' => true],
                'getUser'           =>  ['class'    =>  'user_login',               'function_name' => 'getUserById', 'needToken' => true],
                'createUser'        =>  ['class'    =>  'user_login',               'function_name' => 'createUser', 'needToken' => true],
                'updateUser'        =>  ['class'    =>  'user_login',               'function_name' => 'updateUser', 'needToken' => true],
                'deleteUser'        =>  ['class'    =>  'user_login',               'function_name' => 'deleteUser', 'needToken' => true],
                'logout'            =>  ['class'    =>  'user_login',               'function_name' => 'logoutUser', 'needToken' => false],
                'login'             =>  ['class'    =>  'user_login',               'function_name' => 'loginUser' , 'needToken' => false],
                'listInstructors'   =>  ['class'    =>  'instructors_processor',    'function_name'=>'listInstructors', 'needToken' => false],
                'newInstructor'     =>  ['class'    =>  'instructors_processor',    'function_name'=>'createInstructor', 'needToken' => true],
                'updateInstructor'  =>  ['class'    =>  'instructors_processor',    'function_name'=>'updateInstructor', 'needToken' => true],
                'getInstructor'     =>  ['class'    =>  'instructors_processor',    'function_name'=>'getInstructor', 'needToken' => false],
                'delInstructor'     =>  ['class'    =>  'instructors_processor',    'function_name' =>'deleteInstructor', 'needToken' => true],
                'uploadimg'         =>  ['class'    =>  'upload_processor',         'function_name' => 'uploadAvatar', 'needToken' => true],
                'getMsg'            =>  ['class'    =>  'announcements_processor',  'function_name' => 'getByAnnouncementById', 'needToken' => false],
                'listMsgs'          =>  ['class'    =>  'announcements_processor',  'function_name' => 'listAllAnnouncements', 'needToken' => false],
                'msgLoc'            =>  ['class'    =>  'announcements_processor',  'function_name' => 'listAnnouncementLocations', 'needToken' => false],
                'newMsg'            =>  ['class'    =>  'announcements_processor',  'function_name' => 'createAnnouncement', 'needToken' => true],
                'delMsg'            =>  ['class'    =>  'announcements_processor',  'function_name' => 'deleteAnnouncement', 'needToken' => true],
                'modMsg'            =>  ['class'    =>  'announcements_processor',  'function_name' => 'updateAnnouncement', 'needToken' => true],
                'locMsgs'           =>  ['class'    =>  'announcements_processor',  'function_name' => 'getAnnouncementsByLocation', 'needToken' => false],
                'classes'           =>  ['class'    =>  'hebamio_proc',             'function_name' => 'requestHebamio', 'needToken' => false],
                'faqs'              =>  ['class'    =>  'api_faq',                  'function_name' => 'listFaqs', 'needToken' => false],
                'newFaq'            =>  ['class'    =>  'api_faq',                  'function_name' => 'createFaq', 'needToken' => true],
                'getFaq'            =>  ['class'    =>  'api_faq',                  'function_name' => 'getFaqById', 'needToken' => true],
                'editFaq'           =>  ['class'    =>  'api_faq',                  'function_name' => 'changeFaq', 'needToken' => true],
                'delFaq'            =>  ['class'    =>  'api_faq',                  'function_name' => 'deleteFaqById', 'needToken' => true]
            ];

        }

    }


?>