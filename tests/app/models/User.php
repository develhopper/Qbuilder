<?php
namespace app\models;

use QB\QBuilder;

class User extends QBuilder{
    protected $table="users";
    protected $fields=[
        "username","password","flag"
    ];
    protected $pivot_table=[
        "flight"=>"ticket:user_id:flight_id"
    ];

    public function tickets(){
        return $this->hasMany("\app\models\Ticket");
    }

    public function flights(){
        return $this->belongsToMany("\app\models\Flight");
    }

    public function profile(){
        return $this->hasOne("app\models\Profile");
    }
}
?>