<?php

namespace PragmaRX\TestsWatcher\Vendor\Laravel\Entities;

class Queue extends Model
{
	protected $table = 'ci_queue';

	protected $fillable = [
		'test_id',
	];

	public function test()
	{
		return $this->belongsTo('PragmaRX\TestsWatcher\Vendor\Laravel\Entities\Test');
	}
}
