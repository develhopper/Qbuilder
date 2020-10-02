<?php

include __DIR__."/../vendor/autoload.php";
include __DIR__."/autoload.php";

use PHPUnit\Framework\TestCase;
use models\Product;
use models\ProductLine;

class UnitTest extends TestCase{

    public function testSelect(){
        $product=new Product();
        $this->assertNotEmpty($product->select()->get());
        $this->assertNotEmpty($product->select()->where("productLine","Motorcycles")->get());
        $this->assertNotEmpty($product->select()->where("quantityInStock",">",100)->get());
    }

    public function testInsert(){
        $params=["ProductLine"=>"testLine","textDescription"=>"test description"];
        $line=new ProductLine();

        $this->assertTrue($line->save($params)!=-1);
    }

    public function testDelete(){
        $line=new ProductLine();
        $line->delete("productLine","testLine")->execute();
        $this->assertEmpty($line->select()->where("productLine","testLine")->get());
    }

    public function testUpdate(){
        $product=new Product();
        $product=$product->select()->where("productCode","S24_2000")->first();
        $q=$product->quantityInStock;
        $product->quantityInStock+=1;
        $result=$product->update(true);

        $this->assertTrue($result>0);
    }

}