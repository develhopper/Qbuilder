<?php
namespace app\models;

use QBuilder;

class User extends QBuilder{
    protected $table="users";
    protected $fields=[
        "username","password","flag"
    ];
}
?>