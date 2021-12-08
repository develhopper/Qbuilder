<?php
namespace models;

use QB\QBuilder as Model;

class Country extends Model{
	protected $table="country";
	protected $primary="Code";
}
