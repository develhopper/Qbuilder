<?php
namespace app\models;

use QBuilder as Model;

class Ticket extends Model{
    protected $table="ticket";
    protected $fields=[
        "user_id","flight_id"
    ];
    protected $foreign_keys=[
        "users"=>"user_id"
    ];
}