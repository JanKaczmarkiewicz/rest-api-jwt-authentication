<?php
header("Access-Control-Allow-Origin: http://localhost/rest-api-authentication-example/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/core.php';
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';

include_once '../config/Database.php';
include_once '../model/User.php';

use \Firebase\JWT\JWT;

$db = new Database();
$conn = $db->Connect();

$user = new User($conn);

$user->user_name = "redagast12";

$username_exists = $user->isUserName();

if($username_exists && password_verify("test" , $user->password)){
    
    http_response_code(200);

    $token = array(
        "iss" => $iss,
        "aud" => $aud,
        "iat" => $iat,
        "nbf" => $nbf,
        "data" => array(
            "id" => $user->id,
            "firstname" => $user->first_name,
            "lastname" => $user->last_name,
            "username" => $user->user_name
        )
    );

    $jwt = JWT::encode($token, $key);
    echo json_encode(
            array(
                "message" => "Successful login.",
                "jwt" => $jwt
            )
    );

} else {
    http_response_code(401);

    echo json_encode(array("message" => "Login failed."));
}

?>