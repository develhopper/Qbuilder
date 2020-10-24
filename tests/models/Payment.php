<?php
namespace models;

use QB\QBuilder as Model;

class Payment extends Model{
    protected $table="payments";
    protected $primary="checkNumber";
    protected $related_tables=["customers"=>"customerNumber"];
}