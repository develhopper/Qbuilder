<?php
namespace app\models;

use QB\QBuilder;

class Profile extends QBuilder{
    protected $table="profile";
    protected $fields=[
        "lastname","firstname","user_id"
    ];
    protected $related_tables=[
        "users"=>"user_id"
    ];

    public function user(){
        return $this->belongsTo("app\models\User");
    }
}