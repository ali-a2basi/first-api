<?php

namespace App\Services;


class ProvinceService{


    public function getProvinces($data = null){


        $result = getProvinces($data);

        return $result;

    }

    public function addProvince($data){

        $result = addProvince($data);
        return $result;
    }

    public function deleteProvince($data){

        $result = deleteProvince($data);
        return $result;

    }


    public function updateProvinces($province_id, $name){
        
        $result = changeProvinceName($province_id, $name);
        return $result;
    }
}