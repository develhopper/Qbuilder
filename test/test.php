<?php

use app\models\Profile;
use app\models\User;

require '../load.php';
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
$user=$user->find(1);
$profile=$user->profile();
if(!$profile){
    $profile=new Profile();
    $profile->save(["lastname"=>"alireza","firstname"=>"tajadod","user_id"=>$user->id]);
}
var_dump($profile);