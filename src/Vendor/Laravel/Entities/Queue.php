<?php

namespace PragmaRX\Ci\Vendor\Laravel\Entities;

class Queue extends Model
{
	protected $table = 'ci_queue';

	protected $fillable = [
		'test_id',
	];

	public function test()
	{
		return $this->belongsTo('PragmaRX\Ci\Vendor\Laravel\Entities\Test');
	}
}
