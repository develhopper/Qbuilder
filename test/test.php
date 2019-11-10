<?php

use app\models\User;

require '../load.php';
$user=new User();
$users=$user->execQuery("select * from users");
var_dump($users);