<?php

namespace PragmaRX\Ci\Vendor\Laravel\Entities;

use Illuminate\Database\Eloquent\Model;

class Run extends Model {

	protected $fillable = [
		'test_id',
		'was_ok',
	    'log',
	    'html',
	    'png',
	];

}
