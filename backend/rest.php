<?php
    header('Access-Control-Allow-Origin: *'); 
    header('Access-Control-Allow-Methods: GET, POST');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Authorization');
    // Checks and defines constant to which prevents direct access.
    if(!defined("CONST_KEY")){define("CONST_KEY", "035416f4-e65b-4fc6-a8db-301604ff31c5");}

    $requestMethod = $_SERVER['REQUEST_METHOD'];
    require_once("./autoloader.php");

    if(in_array($requestMethod, ["GET", "POST"])){
        
        $requestMethodArray = array();
        $requestMethodArray = $_REQUEST;
        $token = "";
        $adminToken = false;
        if(isset($requestMethodArray["apiToken"])){$token = $requestMethodArray["apiToken"];}
        if(isset($requestMethodArray["apiFunc"])){ $functionName = $requestMethodArray["apiFunc"];}
        //if(isset($requestMethodArray["apiParams"])){ $functionParams = $requestMethodArray["apiParams"];}
        $functionParams = array();
        foreach($requestMethodArray as $key => $value){
            if($key != 'apiToken' && $key != 'apiFunc'){
                $functionParams[$key] = $value;
            }
        }
        if($token == "" && isset($_COOKIE["apiToken"])){
            $token = $_COOKIE["apiToken"];
            $adminToken = true;
        }

        $postBody = json_decode(file_get_contents("php://input"), true);
        $functionParams["post_body"] = $postBody;
        $functionParams["files"] = $_FILES;

        $cApiHandler = new api_handler();
        $res;
        if($functionName === 'getToken'){
            $res = api_response::getResponse(200);
        }else{
            if($token == ""){
                $res = api_response::getResponse(400);
                $res["message"] = "apiToken failure likely in cookies.";
                $res["cookie"] = $_COOKIE;
                $res["request"] = $requestMethodArray;
                $res["params"] = $functionParams;
            }else{
                $res = api_token::validate($token, $adminToken);
            }
        }

        if($res['status'] !== 200){
            $returnArray = json_encode($res);
            echo $returnArray;
        }else{
            $res = $cApiHandler->callApiFunction($functionName, $functionParams);
            $res["ref"] = $_SERVER["SERVER_NAME"] . $_SERVER["CONTEXT_PREFIX"];
            $returnArray = json_encode($res);

            echo $returnArray;
        }

        if(isset($cApiHandler)){unset($cApiHandler);}

    }else{
        echo(json_encode(api_response::getResponse(405)));
    }


?>