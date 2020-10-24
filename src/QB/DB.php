<?php
namespace QB;

use PDO;

class DB{
    private static $connection;
    private static $options=[
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
    ];
    private function __construct(){

    }

    public static function getInstance(){
        if(!isset(static::$connection)){
            static::$connection=new PDO(self::getDNS(),DB_USER,DB_PASSWORD);
        }
        return self::$connection;
    }

    public static function getDns(){
        return DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME. ";charset=utf8";
    }
}
?>
