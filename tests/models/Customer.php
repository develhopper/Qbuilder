<?php
namespace models;

use QB\QBuilder as Model;

class Customer extends Model{
    protected $table="customers";
    protected $primary="customerNumber";

    public function payments(){
        return $this->hasMany(Payment::class,false);
    }
}