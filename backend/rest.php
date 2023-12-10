<?php
     /*
        This this class is used to consume the Hebamio API and provide a result of all classes and information in a single JSON structure.
        Copyright (c) 2021 - 2023 Jason Reed
        
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
    
    header('Access-Control-Allow-Origin: *'); 
    header('Access-Control-Allow-Methods: GET, POST');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Authorization');
    header('Content-Type: application/json;charset=utf-8');
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
        
        $res = $cApiHandler->callApiFunction($functionName, $functionParams, $token, $adminToken);
        if(is_array($res)){
        $res["ref"] = $_SERVER["SERVER_NAME"];
            if(isset($_SERVER["CONTEXT_PREFIX"])){
                $res["ref"] .=  $_SERVER["CONTEXT_PREFIX"];
            }
        }   
        $returnArray = json_encode($res,JSON_UNESCAPED_UNICODE);
        if($returnArray === null || trim($returnArray) === ''){
            $returnArray = '{"status":500, "Exception":"Empty Result"}';
        }
        echo $returnArray;
       

        if(isset($cApiHandler)){unset($cApiHandler);}

    }else{
        echo(json_encode(api_response::getResponse(405)));
    }


?>