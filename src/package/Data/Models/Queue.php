<?php

namespace PragmaRX\TestsWatcher\Package\Data\Models;

class Queue extends Model
{
    protected $table = 'ci_queue';

    protected $fillable = [
        'test_id',
    ];

    /**
     * Test relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function test()
    {
        return $this->belongsTo('PragmaRX\TestsWatcher\Package\Data\Models\Test');
    }
}
