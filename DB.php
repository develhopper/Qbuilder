<?php
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
            static::$connection=new PDO(getDNS(),USER,PASSWORD);
            echo "not set";
        }
        return self::$connection;
    }
}
?>