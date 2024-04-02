<?php
     /*
        Response builder
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
                case 302:
                    $response["success"] = true;
                    $resposne["status"] = 302;
                    $response["message"] = "Already Exists";
                case 400:
                    $response["success"] = false;
                    $response["status"] = 400;
                    $response["message"] = "Bad Request, expected fields are missing";
                    break;
                case 403:
                    $response["success"] = false;
                    $response["status"] = 403;
                    $response["message"] = "Invalid Token Access Not Allowed.";
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
                    $response["code"] = $statusCode;
            }
            return $response;
        }

    }

?>