<?php
include "autoload.php";
include __DIR__."/../vendor/autoload.php";

use QB\Migration\Migration;
use QB\Migration\Column;
use QB\DB;
use Denver\Env;

Env::setup(__DIR__."/.env");

$users = Migration::create_table('users', Column::IntegerField('id', ['primary' => true]),
    Column::StringField('username',25,['unique' => true]),
    Column::StringField('email',255,['unique' => true]),
    Column::StringField('password',255)
);

$profile = Migration::create_table('profile', Column::IntegerField('id', ['primary']),
    Column::StringField('first_name', 30),
    Column::StringField('last_name', 30),
    Column::IntegerField('user_id', ['connect' => $users->id, 'on_delete' => 'cascade', 'on_update' => 'restrict'])
);

print("DONE");