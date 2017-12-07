<?php

namespace PragmaRX\Tddd\Package\Data\Models;

class Test extends Model
{
    protected $table = 'tddd_tests';

    protected $fillable = [
        'suite_id',
        'path',
        'name',
        'state',
        'sha1',
    ];

    /**
     * Get the full path.
     *
     * @param $value
     *
     * @return mixed|string
     */
    public function getFullPathAttribute($value)
    {
        return make_path([$this->path, $this->name]);
    }

    /**
     * Suite relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function suite()
    {
        return $this->belongsTo('PragmaRX\Tddd\Package\Data\Models\Suite');
    }

    /**
     * Get the test command.
     *
     * @param $value
     *
     * @return string
     */
    public function getTestCommandAttribute($value)
    {
        $command = $this->suite->testCommand;

        return $command.' '.$this->fullPath;
    }

    /**
     * Runs relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function runs()
    {
        return $this->hasMany('PragmaRX\Tddd\Package\Data\Models\Run');
    }

    /**
     * Update test sha1.
     */
    public function updateSha1()
    {
        $this->sha1 = @sha1_file($this->fullPath);

        $this->save();
    }

    /**
     * Check if the sha1 changed.
     *
     * @return bool
     */
    public function sha1Changed()
    {
        return $this->sha1 !== @sha1_file($this->fullPath);
    }
}
