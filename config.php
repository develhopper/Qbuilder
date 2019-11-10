<?php
define("DRIVER","mysql");
define("HOST","localhost");
define("USER","root");
define("PASSWORD","mysql.passwd");
define("DB","airline");
function getDNS(){
    return DRIVER.":host=".HOST.";dbname=".DB;
}
function debug($msg){
    echo "**DEBUG $msg **";
}
?>