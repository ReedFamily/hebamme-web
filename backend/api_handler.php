<?php
    if(!defined("CONST_KEY") || CONST_KEY !== "035416f4-e65b-4fc6-a8db-301604ff31c5"){
        header("Location: ../404.html", true, 404);
        echo file_get_contents('../404.html');
	    die;
    }

    class api_handler
    {
        private $function_map;

        public function __construct(){
            $this->loadFunctionMap();
        }
        
        public function callApiFunction($apiFunction, $apiParams){
            $res = $this->getCommand($apiFunction);
            if($res['success'] === true){
                $class = $res["dataArray"]["class"];
                $func = $res["dataArray"]["function_name"];
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
                'getToken' => ['class' => 'api_token', 'function_name' => 'getToken'],
                'tokenValid' => ['class'=>'api_token', 'function_name'=> 'tokenValid'],
                'sendContact' => ['class' => 'email_processor', 'function_name' => 'send_contact'],
                'listUsers' =>['class' => 'user_login', 'function_name' =>'listUsers'],
                'getUser' => ['class' => 'user_login', 'function_name' => 'getUserById'],
                'createUser' =>['class' => 'user_login', 'function_name' => 'createUser'],
                'updateUser' =>['class'=>'user_login', 'function_name' => 'updateUser'],
                'deleteUser' =>['class'=>'user_login', 'function_name' => 'deleteUser'],
                'logout' =>['class'=>'user_login','function_name' => 'logoutUser'],
                'login' => ['class' => 'user_login', 'function_name' => 'loginUser' ],
                'testMessage' => ['class'=>'email_processor' , 'function_name' => 'sendTestMessage'],
                'listInstructors' => ['class' => 'instructors_processor', 'function_name'=>'listInstructors'],
                'newInstructor' => ['class' =>'instructors_processor', 'function_name'=>'createInstructor'],
                'updateInstructor' => ['class' => 'instructors_processor', 'function_name'=>'updateInstructor'],
                'getInstructor' =>['class' => 'instructors_processor', 'function_name'=>'getInstructor'],
                'delInstructor' =>['class' => 'instructors_processor', 'function_name' =>'deleteInstructor'],
                'uploadimg' =>['class' => 'upload_processor', 'function_name' => 'uploadAvatar'],
                'getMsg' =>['class'=>'announcements_processor', 'function_name' => 'getByAnnouncementById'],
                'listMsgs' =>['class'=>'announcements_processor', 'function_name' => 'listAllAnnouncements'],
                'msgLoc' =>['class'=>'announcements_processor', 'function_name' => 'listAnnouncementLocations'],
                'newMsg' =>['class'=>'announcements_processor', 'function_name' => 'createAnnouncement'],
                'delMsg' =>['class'=>'announcements_processor', 'function_name' => 'deleteAnnouncement'],
                'modMsg' =>['class'=>'announcements_processor', 'function_name' => 'updateAnnoucnement'],
                'locMsgs' =>['class' => 'announcements_processor', 'function_name' => 'getAnnouncementsByLocation'],
                'classes' => ['class' => 'hebamio_proc', 'function_name' => 'requestHebamio']
            ];

        }

    }


?>