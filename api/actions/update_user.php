<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/core.php';
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

include_once '../config/database.php';
include_once '../objects/user.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));
 
$user = new User($db);

$jwt=isset($data->jwt) ? $data->jwt : "";

if($jwt){
 
    try {
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $user->firstname = $data->first_name;
        $user->lastname = $data->last_name;
        $user->password = $data->password;
        $user->id = $decoded->data->id;

        if($user->update()){
            $token = array(
                "iss" => $iss,
                "aud" => $aud,
                "iat" => $iat,
                "nbf" => $nbf,
                "data" => array(
                    "id" => $user->id,
                    "firstname" => $user->first_name,
                    "lastname" => $user->last_name,
                )
             );
             $jwt = JWT::encode($token, $key);

             http_response_code(200);
            
             echo json_encode(
                     array(
                         "message" => "User was updated.",
                         "jwt" => $jwt
                     )
             );
        }else{
 
            // set response code
            http_response_code(401);
         
            // tell the user access denied
            echo json_encode(array("message" => "Access denied."));
        }
 
    }catch (Exception $e){

        http_response_code(401);
        
        echo json_encode(array(
            "message" => "Access denied.",
            "error" => $e->getMessage()
        ));
    }
}else{
    http_response_code(401);
    echo json_encode(array("message" => "Unable to update user."));
}
?>