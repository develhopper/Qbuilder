<?php

use app\models\User;

require '../load.php';
$user=new User();
// $r=$user->select()->where("id",">=",2)->get();
// $r=$user->all();
$param= [
"username" => "smith",
"password" => "pass","flag"=>1];
$r=$user->save($param);
var_dump($r);