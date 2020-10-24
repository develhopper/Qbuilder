<?php
include "autoload.php";
include __DIR__."/../vendor/autoload.php";
use models\Customer;
use models\Payment;

$model=new Customer();
$customer=$model->select()->where("customerNumber",103)->get();
var_dump($customer[0]->payments());