<?php

namespace PragmaRX\Tddd\Package\Data\Models;

class Queue extends Model
{
    protected $table = 'tddd_queue';

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
        return $this->belongsTo('PragmaRX\Tddd\Package\Data\Models\Test');
    }
}
