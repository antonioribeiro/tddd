<?php

namespace PragmaRX\Ci\Vendor\Laravel\Entities;

class Run extends Model
{
    protected $table = 'ci_runs';

    protected $fillable = [
		'test_id',
		'was_ok',
	    'log',
	    'html',
	    'png',
	];
}
