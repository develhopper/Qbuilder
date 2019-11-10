<?php

use app\models\User;

require '../load.php';
$users=User::all();
var_dump($users);