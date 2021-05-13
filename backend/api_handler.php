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
                'sendContact' => ['class' => 'email_sender', 'function_name' => 'send_contact'],
                'listUsers' =>['class' => 'user_login', 'function_name' =>'listUsers'],
                'createUser' =>['class' => 'user_login', 'function_name' => 'createUser'],
                'login' => ['class' => 'user_login', 'function_name' => 'loginUser' ],
                'testMessage' => ['class'=>'email_sender' , 'function_name' => 'sendTestMessage']
            ];

        }

    }


?>