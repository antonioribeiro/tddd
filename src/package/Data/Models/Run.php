<?php

namespace PragmaRX\TestsWatcher\Package\Data\Models;

class Run extends Model
{
    protected $table = 'ci_runs';

    protected $dates = [
        'created_at',
        'updated_at',
        'started_at',
        'ended_at',
        'notified_at',
    ];

    protected $fillable = [
        'test_id',
        'was_ok',
        'log',
        'html',
        'screenshots',
        'started_at',
        'ended_at',
        'notified_at',
    ];
}
