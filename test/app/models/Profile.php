<?php
namespace app\models;

use QBuilder;

class Profile extends QBuilder{
    protected $table="profile";
    protected $fields=[
        "lastname","firstname","user_id"
    ];
    protected $foreign_keys=[
        "users"=>"user_id"
    ];

    public function user(){
        return $this->belongsTo("app\models\User");
    }
}