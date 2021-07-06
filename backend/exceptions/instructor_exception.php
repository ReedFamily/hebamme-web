 <?php
 if(!defined("CONST_KEY") || CONST_KEY !== "035416f4-e65b-4fc6-a8db-301604ff31c5"){
        header("Location: ../404.html", true, 404);
        echo file_get_contents('../404.html');
	    die;
    }


    class InstructorException extends Exception
    {
       public function __construct($message, $code = 0, $previous = null){
           parent::__construct($message,$code, $previous);
       } 

       public function __toString(){
           return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
       }
    }

?>