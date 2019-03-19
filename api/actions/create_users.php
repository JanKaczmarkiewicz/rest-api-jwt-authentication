<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");//Maximum number of seconds the results can be cached.
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
include_once("../config/Database.php");
include_once("../config/dataRestrictions.php");
include_once("../model/User.php");

$db = new Database();
$conn = $db->Connect();

$user = new User($conn);

$data = json_decode(file_get_contents("php://input"));


$user->user_name= $data->username;
$user->last_name= $data->lastname;
$user->first_name= $data->firstname;
$user->password= $data->password;

if($user->testUserData($rules)){

   if($user->createUser()){
      http_response_code(200);
      echo json_encode(array(
         "message" => "User has been created succisfully"
         ));
   }else {
      http_response_code(401);
      echo json_encode(array(
         "message" => "We got some problems. Try again :>"
         ));
   }
}
 




?>