<?php

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use Illuminate\Support\Arr;

try {
    $pdo = new PDO("mysql:dbname=iran;host=localhost", 'root', '');
    $pdo->exec("set names utf8;");
    // echo "Connection OK!";
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

#==============  Simple Validators  ================
function isValidCity($data){
    if(empty($data['province_id']) or !is_numeric($data['province_id']))
        return false;
    return empty($data['name']) ? false : true;
}
function isValidProvince($data){
    return empty($data['name']) ? false : true;
}



$users = array(


    (object)['id'=>'1' ,'name'=>'ali', 'email'=>'ali@gmail.com', 'role'=>'admin' ,'province_allowed'=>[1]],
    (object)['id'=>'2' ,'name'=>'mohammad', 'email'=>'mohammad@gmail.com', 'role'=>'gevernor' ,'province_allowed'=>[7, 1, 2, 13  ]],
    (object)['id'=>'3' ,'name'=>'sara', 'email'=>'sara@gmail.com', 'mayer'=>'admin' ,'province_allowed'=>[3]],
    (object)['id'=>'4' ,'name'=>'nicoleaniston', 'email'=>'nicoleaniston@gmail.com', 'mayer'=>'president' ,'province_allowed'=>[13, 11, 4]]
);




function getUserById($id){
    global $users;

    foreach($users as $user){
        
        
        

        if($user->id === $id){

            return $user;
        }
    return null;
    }
}





function getUserByEmail($email){

    global $users;

    foreach($users as $user){

        if(strtolower($user->email) === strtolower($email)){

            return $user;
        }
    return null;
    }


}


//The trim() function removes whitespace
//and other predefined characters from both sides of a string.


function getAuthorizationHeader(){
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    }
    else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        //print_r($requestHeaders);
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    return $headers;
}
/**
* get access token from header
* */
function getBearerToken() {
$headers = getAuthorizationHeader();
// HEADER: Get the access token from the header
if (!empty($headers)) {
    if (preg_match('/Bearer\s(\S+)/', $headers , $matches)) {
        return $matches[1];
    }
}
return null;
}



#================  Read Operations  =================
function getCities($data = null){
    global $pdo;
    $province_id = $data['province_id'] ?? null;
    $where = '';
    $page = $data['page'] ?? null;
    $pagesize = $data['pagesize'] ?? null;
    $limit = '';

    if(is_numeric($page) and is_numeric($pagesize)){
        $start = $pagesize*($page-1);

        $limit = " LIMIT $start, $pagesize";
    }
    //setting $province_id to integer value
    if(isset($province_id)? intval($province_id):null){
        $where = "where province_id = $province_id";
    }
    $sql = "select * from city $where $limit";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_OBJ);
    return $records;
}
function getProvinces($id = null){
    global $pdo;
    // echo gettype($id);
    $where = "";
    
    if(isset($id)?intval($id): null){

        $where = "WHERE id = $id";
    }
    $sql = "SELECT * FROM province $where";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_OBJ);
    return $records;
}


#================  Create Operations  =================
function addCities($data){
    global $pdo;
    if(!isValidCity($data)){
        return false;
    }
    $sql = "INSERT INTO `city` (`province_id`, `name`) VALUES (:province_id, :name);";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':province_id'=>$data['province_id'],':name'=>$data['name']]);
    return $stmt->rowCount();
}
function addProvince($data){
    global $pdo;
    if(!isValidProvince($data)){
        return false;
    }
    $sql = "INSERT INTO `province` (`name`) VALUES (:name);";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':name'=>$data['name']]);
    return $stmt->rowCount();
}


#================  Update Operations  =================
function changeCityName($city_id,$name){
    global $pdo;
    $sql = "UPDATE city SET name = :name WHERE id = :city_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':city_id'=>$city_id, ':name'=>$name]);
    return $stmt->rowCount();
}
function changeProvinceName($province_id,$name){
    global $pdo;
    $sql = "update province set name = '$name' where id = $province_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount();
}

#================  Delete Operations  =================
function deleteCities($city_id){
    global $pdo;
    $sql = "delete from city where id = $city_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount();
}
function deleteProvince($province_id){
    global $pdo;
    $sql = "delete from province where id = $province_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount();
}



function generateApiToken($user){

    $payload = array(

        'user_id' => $user->id
    );

    $jwt = JWT::encode($payload, JWT_KEY, JWT_ALG);

    return $jwt;


}



function tokenDecoding($token){

    $payload = JWT::decode($token, new Key(JWT_KEY, JWT_ALG));
    return getUserById($payload->user_id);


}



function isValidToken($token){
    global $token;

    try{
        
       $result = tokenDecoding($token);
       return $result;


    }catch(Exception $e){ 
        return false;
    }
}


function hasAllowedToProvince($data_user){

    if(IsValidToken($data_user)){
        $province_allowed = $data_user->province_allowed;
        foreach($province_allowed as $province_id_allowed){

            return $province_id_allowed;

        }

    }


}

// Function Tests
// $data = addCity(['province_id' => 23,'name' => "Loghman Shahr"]);
// $data = addProvince(['name' => "7Learn"]);
// $data = getCities(['province_id' => 23]);
// $data = deleteProvince(34);
// $data = changeProvinceName(34,"سون لرن");
// $data = getProvinces();
// $data = deleteCity(443);
// $data = changeCityName(445,"لقمان شهر");
// $data = getCities(['province_id' => 1]);
// $data = json_encode($data);
// echo "<pre>";
// print_r($data);
// echo "<pre>";
