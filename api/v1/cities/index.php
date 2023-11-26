<?php
include_once 'C:\xampp\htdocs\learn.php\Iran\loader.php';
use \App\Services\CityService;
use App\Utilities\CacheUtility;
use \App\Utilities\Response;
use Illuminate\Support\Facades\Cache;

$request_method =$_SERVER['REQUEST_METHOD'];


$city_service = new CityService();
$city_name = $_GET['name']??null;
$city_id = $_GET['city_id']??null;


$token = getBearerToken();
if(!isValidToken($token)){

    Response::respondAndDie(['InvalidToken'], Response::HTTP_OK);
}

$result = tokenDecoding($token);














// if(!isValidToken($token)){

//     Response::respondAndDie(['Invalid Bearer Token'], Response::HTTP_UNAUTHORIZED);
// }



//php://input is set for getting body of json request
// $request_body = file_get_contents('php://input');

// Response::respondAndDie($request_body);



//Because we have respond and die we can delete break points in co

switch ($request_method) {
    case 'GET':

        if(CacheUtility::cached_exist()){
            Response::setHeaders();
        }
        CacheUtility::start();
        // $validation = new Validation();
        $province_id =hasAllowedToProvince($result) ??null ;

        $get_data_get = [

            'province_id' => $province_id,
            'page' => $_GET['page'] ?? null,
            'pagesize' => $_GET['pagesize']??null,
            
        ];


        
        $result_get = $city_service->getCities($get_data_get);
        if(empty($result_get)){

            Response::respondAndDie('no city found', Response::HTTP_NOT_FOUND);
        }

        echo Response::respond($result_get, Response::HTTP_OK);


        CacheUtility::end();
        break;



    case "POST":
        $province_id_post =  $_GET['province_id']??null;
        


        $get_data_post = [

            'province_id' => $province_id_post,
            'name' => $city_name
        ];
        
        if (!isValidCity($get_data_post)){

            Response::respondAndDie('Invalid City', Response::HTTP_NOT_ACCEPTABLE);
            
        }

        $result_post = $city_service->addCities($get_data_post);

        Response::respondAndDie($result_post, Response::HTTP_CREATED);


    case "PUT":
        $data_get_put = [

            'city_id' => $city_id,
            'name' => $city_name

        ];


        $response_put = $city_service->updateCityName($data_get_put);

        if($city_name or !is_numeric($city_id)){

            Response::respondAndDie('data is invalid', Response::HTTP_NOT_ACCEPTABLE);
        }elseif($response_put === 0){

            Response::respondAndDie('city id is not valid', Response::HTTP_NOT_FOUND);
        }

        Response::respondAndDie($response_put, Response::HTTP_OK);
        break;


    case "DELETE":

        $data_get_delete = [

            'city_id' => $city_id,
            
        
        ];


        if(is_numeric($city_id)){
            $response_delete = $city_service->deleteCities($data_get_delete['city_id']);
            if($response_delete){
                Response::respondAndDie($response_delete, Response::HTTP_DELETED);

            }else{
                Response::respondAndDie('city id is not exist', Response::HTTP_NOT_FOUND);
            }
        }else{
            Response::respondAndDie('city id is not valid', Response::HTTP_NOT_ACCEPTABLE);
        }

        break;

    default:
        Response::respondAndDie('Invalid request method', Response::HTTP_METHOD_NOT_ALLOWED);
}
