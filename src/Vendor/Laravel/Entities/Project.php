<?php

namespace PragmaRX\Ci\Vendor\Laravel\Entities;

use Illuminate\Database\Eloquent\Model;

class Project extends Model {

	protected $fillable = [
		'name',
		'path',
	    'tests_path',
	];

}
