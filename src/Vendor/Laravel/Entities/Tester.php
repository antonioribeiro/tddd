<?php

namespace PragmaRX\Ci\Vendor\Laravel\Entities;

use Illuminate\Database\Eloquent\Model;

class Tester extends Model {

	protected $fillable = [
		'name',
		'command',
	    'ok_matcher',
	];

}
