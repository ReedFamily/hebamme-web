<?php
    if(!defined("CONST_KEY") || CONST_KEY !== "035416f4-e65b-4fc6-a8db-301604ff31c5"){
        header("Location: ../404.html", true, 404);
        echo file_get_contents('../404.html');
        die;
    }

    class email_sender
    {

        private $messageLoader = [
            'contact' => 'messages/contact-email.php',
            'recover' => 'messages/recover-email.php'

        ];

        private $headers = [
            'MIME-Version' => 'MIME-Version: 1.0',
            'Content-type' => 'text/plain; charset=UTF-8',
            'From' => CONST_SEND_TO,
            'Reply-To' => CONST_SEND_TO,
            'X-Mailer' => "PHP/{phpversion()}"
        ];


        public function send_contact($params){
            if(isset($params["post_body"])){
                
                $res = $this->sendMessage($this->buildContactMessageBody($params["post_body"]), "von Kontaktformular");
            }else{
                $res = api_response::getResponse(500);
                $res["extra"] = "post body wasn't properly set";
                $res["params"] = $params;
                log_util::logEntry("error", "No post body provided");
            }
            return $res;
        }

        public function sendRecoveryMessage($email, $specialToken){

        }

        public function sendTestMessage(){
            $message = "Testing message with äüöß to see what happens";
            return $this->sendMessage($message);
        }

        private function sendMessage($message, $subject, $sendTo = null){
           
            if(!isset($sendTo)){
                $sendTo = CONST_SEND_TO;
            }
            $msg = wordwrap($message, 70);
            $res;
            try{
                $sent = mail($sendTo, $subject, $msg, $this->headers);
                if($sent == true){
                    $res = api_response::getResponse(200);
                    log_util::logEntry("debug", "Email message Sent");
                }else{
                    $res = api_response::getResponse(500);
                    $res["returnValue"] = "Message Not Sent";
                    log_util::logEntry("error","Email message not sent");
                }
            }catch(Exception $e){
                $res = api_response::getResponse(500);
                $res["exception"] = $e;
                log_util::logEntry("error", $e->getMessage());
            }
            return $res;
        }


        private function buildContactMessageBody($postBody){
            $post = new post_body_handler($postBody);
            include("messages/contact-email.php");
            return $messageBody;
        }

    }

    class link_builder{
        
        private $token;
        public function __construct($token){
            $this->token = $token;
        }

    }

    class post_body_handler{
        private $postBody;

        public function __construct($postBody){
            $this->postBody = $postBody;
        }

        public function getAnrede(){
            if(isset($this->postBody["anrede"])){
                return $this->postBody["anrede"];
            }else{
                return "";
            }
        }

        public function getLastname(){
            if(isset($this->postBody["lastname"])){
                return $this->postBody["lastname"];
            }else{
                return "";
            }
        }

        public function getFirstname(){
            if(isset($this->postBody["firstname"])){
                return $this->postBody["firstname"];
            }else{
                return "";
            }
        }

        public function getEmail(){
            if(isset($this->postBody["emailAddress"])){
                return $this->postBody["emailAddress"];
            }else{
                return "keins gegeben.";
            }
        }

        public function getPhone(){
            if(isset($this->postBody["phone"])){
                return $this->postBody["phone"];
            }else{
                return "keins gegeben.";
            }
        }

        public function getAddress(){
            $street = $this->postBody["address"];
            $city = $this->postBody["city"];
            $zip = $this->postBody["zip"];

            return <<<EOT
$street
$zip $city
EOT;
        }

        public function getPreferredContact(){
            $contactByEmail = false;
            $contactByPhone = false;
            if(isset($this->postBody["contactByEmail"]) && $this->postBody["contactByEmail"] == 'y'){
                $contactByEmail = true;
            }
            if(isset($this->postBody["contactByPhone"]) && $this->postBody["contactByPhone"] == 'y'){
                $contactByPhone = true;
            }

            if($contactByPhone && $contactByEmail){
                return "Telefon oder Email";
            }elseif($contactByPhone){
                return "Telefon";
            }elseif($contactByEmail){
                return "Email";
            }   
        }

        public function getMessage(){
            if(isset($this->postBody["message"])){
                return $this->postBody["message"];
            }else{
                return "Es wurde keine Nachricht eingetragen.";
            }
        }

    }

?>