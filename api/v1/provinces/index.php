<?php

include 'C:\xampp\htdocs\learn.php\Iran\loader.php';

use \App\Services\ProvinceService;
use \App\Utilities\Response; 


$request_method = $_SERVER['REQUEST_METHOD'];



$request_body = json_decode(file_get_contents('php://input'), true);

$province_service = new ProvinceService();
$province_id = $_GET['id'] ?? null;
$province_name = $_GET['name'] ?? null;

switch($request_method){

    case "GET":

        $data_get = [

            'id' => $province_id
        ];



        $response_get = $province_service->getProvinces($data_get['id']);
        Response::respondAndDie($response_get, Response::HTTP_OK);
        break;


    case "POST":

        $data_post = [
            'name' => $province_name
        ];


        if(!isValidProvince($data_post)){

            Response::respondAndDie('Province name can not be empty', Response::HTTP_NOT_ACCEPTABLE);
        }

        $response_post = $province_service->addProvince($data_post);

        Response::respondAndDie($response_post, Response::HTTP_OK);
        break;

    default:
        Response::respondAndDie('Invalid request method', Response::HTTP_METHOD_NOT_ALLOWED);



}


