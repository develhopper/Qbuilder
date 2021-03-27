<?php
include "autoload.php";
include __DIR__."/../vendor/autoload.php";
use models\Customer;
use models\Payment;
use QB\DB;
use Denver\Env;

Env::setup(__DIR__."/.env");

echo getenv('DB_DRIVER');
$model=new Customer();
$customer=$model->select()->where("customerNumber",103)->get();
var_dump($customer[0]->payments()->sort("paymentDate","DESC")->get());
