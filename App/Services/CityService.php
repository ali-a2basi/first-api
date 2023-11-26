<?php


namespace App\Services;


class CityService{



    public function getCities($data = null){


        $result = getCities($data);

        return $result;
    }


    public function addCities($data){

        $result = addCities($data);

        return $result;


    }


    public function deleteCities($city_id){


        $result = deleteCities($city_id);

        return $result;
    }


    function updateCityName($data){

        $result = changeCityName($data['city_id'], $data['name']);

        return $result;
    }
}