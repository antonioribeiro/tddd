<?php

namespace PragmaRX\TestsWatcher\Entities;

class Project extends Model
{
    protected $table = 'ci_projects';

    protected $fillable = [
        'name',
        'path',
        'tests_path',
    ];

    /**
     * Get the full path attribute.
     *
     * @return mixed|string
     */
    public function getTestsFullPathAttribute()
    {
        return make_path(
            [
                $this->path,
                $this->tests_path,
            ]
        );
    }
}
