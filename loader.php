<?php
define('CACHE_DIR', "C:/xampp/htdocs/learn.php/Iran/cache/");


include_once 'vendor/autoload.php';
include_once "App/iran.php";


#authorization constants

define('JWT_KEY', 'aliabasi123456789abcd');
define('JWT_ALG', 'HS256');



spl_autoload_register(function ($class) {

    $classFile = __DIR__ . "/". $class . ".php";
// is_readable check whether the file exists and readable

    if(is_readable($classFile)){

        include $classFile;

    }else{

        die("$class not found") ;

    }


    
});
 


