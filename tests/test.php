<?php
require __DIR__.'/../vendor/autoload.php';
require __DIR__ . '/load.php';

use app\models\Flight;
use app\models\Profile;
use app\models\User;
$user=new User();
// $r=$user->select()->where("id",">=",2)->get();
// $r=$user->all();
// $param= [
// "username" => "smith",
// "password" => "pass","flag"=>1];
// $r=$user->save($param);
// $user=$user->find(1);
// $user->username="alireza.tjd77";
// $user->password="password";
// $user->update(true);
// $user->update()->where("username","alireza.tjd")->execute();
// $tickets=$user->find(1)->tickets();
// var_dump($tickets);
// $user=$user->find(1);
// $profile=$user->profile();
// if(!$profile){
//     $profile=new Profile();
//     $profile->save(["lastname"=>"alireza","firstname"=>"tajadod","user_id"=>$user->id]);
// }
// var_dump($profile);
// $flight=new Flight();
// $users=$flight->find(3)->users();
// var_dump($users);

// $flight=new Flight();
// $flight=$flight->select()->first();
// var_dump($flight->users());

$user=$user->select()->first();
var_dump($user->flights());