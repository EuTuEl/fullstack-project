<?php
const ROOT = 'C:\xampp\htdocs\test-api';
require_once ROOT . "/api/Control.php";

//if (!isset($_SESSION["jwt"])) { //init session
//    session_start();
//    $_SESSION["jwt"] = "123default123";
//    $_SESSION["userID"] = -1;
//}

Control::initSession();




$url = $_SERVER["REQUEST_URI"];
$method = explode("/", $url)[4];


switch ($method) {
    case "get":
        $fields = Control::get();
        break;
    case "post":
        $fields = Control::post($_POST["payload"], $_POST["username"], $_POST["token"]);
        break;
    case "delete":
        $fields = Control::delete(explode("/", $url)[5] ?? -1, $_POST["username"], $_POST["token"]);
        break;
//    case "put":
//        $fields = Control::get();
//        createResponse($fields["code"], $fields["success"], $fields["error"], $fields["body"]);
//        break;
    case "register":
        $fields = Control::register($_POST, $_SERVER["PHP_AUTH_USER"], $_SERVER["PHP_AUTH_PW"]);
        break;
    case "login":
        $fields = Control::login($_SERVER["PHP_AUTH_USER"], $_SERVER["PHP_AUTH_PW"]);
        break;
}

createResponse($fields["code"], $fields["success"], $fields["error"], $fields["body"]);



function createResponse($response_code, $success, $error, $body): void {
    header("Content-Type:application/json");
    http_response_code($response_code);

    $response['success'] = $success;
    $response['error'] = $error;

    $response['body'] = $body;

    $json_response = json_encode($response);
    echo $json_response;
}