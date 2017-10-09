<?php

namespace PragmaRX\TestsWatcher\Vendor\Laravel\Entities;

class Project extends Model
{
    protected $table = 'ci_projects';

    protected $fillable = [
        'name',
        'path',
        'tests_path',
    ];

    public function getTestsFullPathAttribute($value)
    {
        return make_path(
            [
                $this->path,
                $this->tests_path,
            ]
        );
    }
}
