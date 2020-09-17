<?php
namespace app\models;

use QB\QBuilder;

class Flight extends QBuilder{
    protected $table="flight";
    protected $fields=[
        "origin","destination","departure","arrival"
    ];
    protected $pivot_table=[
        "users"=>"ticket:flight_id:user_id"
    ];
    
    public function users(){
        return $this->belongsToMany("app\models\User");
    }
}