<?php

use app\models\User;

require '../load.php';
$user=new User();
// $r=$user->select()->where("id",">=",2)->get();
// $r=$user->all();
// $param= [
// "username" => "smith",
// "password" => "pass","flag"=>1];
// $r=$user->save($param);
$user=$user->find(1);
$user->username="alireza.tjd77";
$user->password="password";
// $user->update(true);
var_dump($user->update()->where("username","alireza.tjd")->execute());