<?php


use \Firebase\JWT\JWT;

require 'vendor/autoload.php';
require 'config/setup.php';
use flight\Engine;

$app = new Engine();

$app->route('/', function(){
    
    Flight::render('index');
    
});
$app->route('/registrationsuccessful', function(){

    Flight::render('registrationsuccessful');
    
});
$app->route('GET /login', function() {
    
        Flight::render('login');
        
});

$app->route('POST /login', function() {
    $username = Flight::request()->data['username'];
    $password = Flight::request()->data['password'];
    $user = Flight::users();
    $db = Flight::db();
    $return = json_decode($user->login($db,$username,$password),true);
    if ($return['status'] == "success") {
        
                            
                            //$tokenId    = base64_encode(mcrypt_create_iv(32)); // mcrypt_create_iv is depricated in PHP 7.1
                            $tokenId    = base64_encode(random_bytes(32));
                            $issuedAt   = time();
                            $notBefore  = $issuedAt + 10;  //Adding 10 seconds
                            $expire     = $notBefore + 7200; // Adding 60 seconds
                            $serverName = HTTP_HOST; /// set your domain name

                            /*
                             * Create the token as an array
                             */
                            $data = [
                                'iat'  => $issuedAt,         // Issued at: time when the token was generated
                                'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token
                                'iss'  => $serverName,       // Issuer
                                'nbf'  => $notBefore,        // Not before
                                'exp'  => $expire,           // Expire
                                'data' => [                  // Data related to the logged user you can set your required data
                                    'id'   => $return['userid'], // id from the users table
                                    'username' => $username, // name
                                ]
                            ];
                          $secretKey = base64_decode("G0dASh0ftH3W0r1d");
                          /// Here we will transform this array into JWT:
                          $jwt = JWT::encode(
                                    $data, //Data to be encoded in the JWT
                                    $secretKey, // The signing key
                                    ALGORITHM // The algorithm to use for encoding
                          );

                         $_SESSION['userid'] = $return['userid'];
                         $result = $db->prepare("SELECT usertype FROM users WHERE id = {$_SESSION['userid']}");
                         $result->execute();
                         $_SESSION['usertype'] = $result->fetchAll();
                         
                         //$unencodedArray = ['jwt' => $jwt];
                         $_SESSION['jwt'] = $jwt;  // Store token in session var so we can get it later
                         Flight::redirect('dashboard');
                         exit();
    } else {
        Flight::render('login', array("errorMessage"=>$return['error'], "username"=>$username));
    }
});
$app->route('POST /register', function() {
        $username = Flight::request()->data['username'];
        $email = Flight::request()->data['email'];
        $password = Flight::request()->data['password'];
        $firstname = Flight::request()->data['firstname'];
        $lastname = Flight::request()->data['lastname'];
        
       
        $db = Flight::db();
        $user = Flight::users();
        
    
        $return = json_decode($user->register($db, $username, $password, $email, $firstname,$lastname),true);
        if ($return['status'] == "success") {
            
          Flight::render('registrationsuccessful');
        } else {
        Flight::render('login', ["errorMessage"=>$return['error']]);
        }

});
$app->route('GET /dashboard', function() {
    //if (is_authorized()) {
    Flight::render('index', array());
    //} else {
    //Flight::render('login', array());
    //}
    
});
$app->route('/characterlookup/@id/@userID', function($id, $userID) {
    $db = Flight::db();
    $character = Flight::character();
    $json = $character->getAll($db, $id);
    $allData = json_decode($json);
    $data = $allData->character[0];
//    $stats = $character->getStats($data->link);
//    print_r($stats);
    Flight::render('characterlookup', array('id' => $id, 'userID' => $userID, 'data' => $data, 'stats' => json_decode($stats)));
        
    
});
$app->route('GET /logout', function() {
    unset($_SESSION['jwt']);
    unset($_SESSION['userid']);
    Flight::redirect('login');
    exit();
});
$app->route('POST /addNewChar', function() {
    $method = Flight::request()->data->method;
    $data = Flight::request()->data->data;
    $db = Flight::db();
    $character = Flight::character();
    print_r($_SESSION);
    switch ($method) {
        case "addNew":
            $result = $character->addNew($db, $data);
            break;
        
    }
    echo $result;
});
$app->start();