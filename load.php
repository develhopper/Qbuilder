<?php
const BASE=__DIR__;
require 'config.php';
spl_autoload_register(function($name){
    $name=BASE."/".str_replace("\\","/",$name).".php";
    if(file_exists($name))
        require_once $name;
        else
        echo "not found";
});
?>