<?php

use app\models\User;

require '../load.php';
$user=new User();
// $r=$user->select()->where("id",">=",2)->get();
$r=$user->all();
var_dump($r);