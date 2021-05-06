<?php
    if(!defined("CONST_KEY") || CONST_KEY !== "035416f4-e65b-4fc6-a8db-301604ff31c5"){
        header("Location: ../404.html", true, 404);
        echo file_get_contents('../404.html');
        die;
    }

    class email_sender
    {
        
        public function send_contact($params){
            if(isset($params["post_body"])){
                $res = api_response::getResponse(200);
                $res["received"] = $params["post_body"];
                $res['body'] = $this->buildMessageBody($params['post_body']);
            }else{
                $res = api_response::getResponse(500);
                $res["extra"] = "post body wasn't properly set";
                $res["params"] = $params;
            }
            return $res;
        }


        private function buildMessageBody($postBody){

            $post = new post_body_handler($postBody);

            $message = <<<EOT
            Jemand hat Sie über das Kontaktformular auf der Website kontaktiert:
            {$post->getAnrede()}
            {$post->getLastname()}, {$post->getFirstname()} 
            Email: {$post->getEmail()} 
            Telefon: {$post->getPhone()} 
            Address:
            {$post->getAddress()} 
            Bitte kontaktieren Sie mich per 
            {$post->getPreferredContact()} 

            Nachricht:
            {$post->getMessage()} 
EOT;

            return $message;

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