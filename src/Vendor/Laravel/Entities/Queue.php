<?php

namespace PragmaRX\Ci\Vendor\Laravel\Entities;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model {

	protected $table = 'queue';

	protected $fillable = [
		'test_id',
	];

	public function test()
	{
		return $this->belongsTo('PragmaRX\Ci\Vendor\Laravel\Entities\Test');
	}

}
