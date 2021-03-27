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
            static::$connection=new PDO(self::getDNS(),
            getenv('DB_USER') ,getenv('DB_PASSWORD'));
        }
        return self::$connection;
    }
    
    public static function getDns(){
        return getenv('DB_DRIVER') . ":host=" . getenv('DB_HOST') .
        ";dbname=" . getenv('DB_NAME') . ";charset=utf8";
    }
}
?>
