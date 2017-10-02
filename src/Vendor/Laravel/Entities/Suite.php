<?php

namespace PragmaRX\TestsWatcher\Vendor\Laravel\Entities;

class Suite extends Model
{
    protected $table = 'ci_suites';

    protected $fillable = [
		'name',
		'project_id',
		'tester_id',
		'tests_path',
		'suite_path',
		'file_mask',
		'command_options',
	    'max_retries',
	];

	public function getTestsFullPathAttribute($value)
	{
		return make_path(
            [
                $this->project->tests_full_path,
                $this->tests_path
            ]
		);
	}

	public function project()
	{
		return $this->belongsTo('PragmaRX\TestsWatcher\Vendor\Laravel\Entities\Project');
	}

	public function tester()
	{
		return $this->belongsTo('PragmaRX\TestsWatcher\Vendor\Laravel\Entities\Tester');
	}

	public function tests()
	{
		return $this->hasMany('PragmaRX\TestsWatcher\Vendor\Laravel\Entities\Test');
	}

	public function getTestCommandAttribute()
	{
		$command = $this->tester->command . ' ' . $this->command_options;

		return str_replace('%project_path%', $this->project->path, $command);
	}
}
