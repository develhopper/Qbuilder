<?php
require __DIR__.'/config.php';

spl_autoload_register(function($name){
    $name=__DIR__."/".str_replace("\\","/",$name).".php";
    if(file_exists($name))
        require_once $name;
    else
        echo "debug::: not found $name \r\n";
});
?>