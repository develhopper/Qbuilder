<?php
namespace models;

use QB\QBuilder as Model;

class Product extends Model{
    protected $table="products";
    protected $primary="productCode";
    protected $fields=[

    ];
}