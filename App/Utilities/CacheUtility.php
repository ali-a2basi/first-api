<?php

namespace App\Utilities;
use \App\Utilities\Response;


class CacheUtility{


    protected static $cache_file;

    protected static $cached_enable = 1;

    const EXPIRE_TIME = 3600;

    public static function init(){

        self::$cache_file = CACHE_DIR . md5($_SERVER['REQUEST_URI']).'.json';
        if($_SERVER['REQUEST_METHOD']!= 'GET'){

            self::$cached_enable = 0;
        }
    }



    public static function cached_exist(){

        return (file_exists(self::$cache_file) and (time()-self::EXPIRE_TIME)<filemtime(self::$cache_file) );
    }

    public static function start(){
//setting cache file
        self::init();
//checking if cache is enable or not
        if(!self::$cached_enable){
            return;
        }

        Response::setHeaders();
        /*
        The filemtime() function in PHP is an inbuilt function
        which is used to return the last time of a specified file 
        when its content was modified.
        The filemtime() function returns the last time the file was changed as a Unix Timestamp on
        success and False on failure.
        */
         
        if(self::cached_exist()){

            readfile(self::$cache_file);
            exit;
        }
        ob_start();
    }
    public static function end(){

        if(!self::$cached_enable){

            return;
        }

        $cachedFile = fopen(self::$cache_file, 'w');

        fwrite($cachedFile, ob_get_contents());
        fclose($cachedFile);

        //alternative
        //file_put_content($file, $content);
        //file_put_contents(self::cache_file, ob_get_content);

        ob_end_flush();
    }

//deleting all cache files
    public static function flush(){
        //glob($string pattern) searching for folders in pattern
        $files = glob(CACHE_DIR.'*');

        foreach($files as $file){

            if(is_file($file)){

                unlink($file);
            }
        }


    }

}