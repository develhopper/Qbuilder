<?php
include "autoload.php";
include __DIR__."/../vendor/autoload.php";
//use models\Customer;
//use models\Payment;
use models\Country;
use QB\DB;
use Denver\Env;

Env::setup(__DIR__."/.env");

echo getenv('DB_DRIVER');
//$model=new Customer();
//$customer=$model->select()->where("customerNumber",103)->get();
//var_dump($customer[0]->payments()->sort("paymentDate","DESC")->get());

$country = new Country();
		
//$data = $country->select("Code")->paginate(1,20)->getArray();

//var_dump($data);
//echo "\n\r";
//$data = $country->select("Code")->paginate(2,20)->getArray();
//var_dump($data);
//
var_dump($country->find("USA"));
