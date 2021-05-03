<?php
    if(!defined("CONST_KEY") || CONST_KEY !== "035416f4-e65b-4fc6-a8db-301604ff31c5"){
        header("Location: ../404.html", true, 404);
        echo file_get_contents('../404.html');
        die;
    }

    class api_response{
        public static function getResponse($statusCode){
            $response["success"] = '';
            $response["status"] = '';
            $response["message"] = '';

            switch($statusCode){
                case 200:
                    $response["success"] = true;
                    $response["status"] = 200;
                    $response["message"] = "Operation successful";
                    break;
                case 400:
                    $response["success"] = false;
                    $response["status"] = 400;
                    $response["message"] = "Bad Request, expected fields are missing";
                    break;
                case 405:
                    $response["success"] = false;
                    $response["status"] = 405;
                    $response["message"] = "Method not allowed. The method specified in the Request-Line is not allowed for the resource identified by the Request-URI.";
                case 500:
                    $response["success"] = false;
                    $response["status"] = 500;
                    $response["message"] = "Server failure";
                default:
                    $response["success"] = false;
                    $response["status"] = 000;
                    $response["message"] ="Unknown application operation.";
            }


            return json_encode($response);
        }

    }

?>