<?php

namespace PragmaRX\Ci\Vendor\Laravel\Entities;

use Illuminate\Database\Eloquent\Model;

class Test extends Model {

	protected $fillable = [
		'suite_id',
		'name',
		'state',
	];

	public function getFullPathAttribute($value)
	{
		return make_path([$this->suite->testsFullPath, $this->name]);
	}

	public function suite()
	{
		return $this->belongsTo('PragmaRX\Ci\Vendor\Laravel\Entities\Suite');
	}

	public function getTestCommandAttribute($value)
	{
		$command = $this->suite->testCommand;

		return $command . ' ' . $this->fullPath;
	}

	public function runs()
	{
		return $this->hasMany('PragmaRX\Ci\Vendor\Laravel\Entities\Run');
	}

}
