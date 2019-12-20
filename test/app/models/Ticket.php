<?php
namespace app\models;

use QB\QBuilder as Model;

class Ticket extends Model{
    protected $table="ticket";
    protected $fields=[
        "user_id","flight_id"
    ];
    protected $related_tables=[
        "users"=>"user_id"
    ];

    public function users(){
        return $this->belongsToMany("app\models\User");
    }
}