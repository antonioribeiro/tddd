<?php

namespace PragmaRX\Tddd\Package\Data\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use PragmaRX\Tddd\Package\Data\Repositories\Data as DataRepository;
use PragmaRX\Tddd\Package\Events\DataUpdated;

class Model extends Eloquent
{
    private function broadcastDataUpdated()
    {
        if (app(DataRepository::class)->projectSha1HasChanged()) {
            broadcast(new DataUpdated());
        }
    }

    /**
     * Save the model to the database.
     *
     * @param array $options
     *
     * @return bool
     */
    public function save(array $options = [])
    {
        parent::save($options);

        $this->broadcastDataUpdated();
    }

    /**
     * Get the connection of the entity.
     *
     * @return string|null
     */
    public function getQueueableConnection()
    {
        // TODO: Implement getQueueableConnection() method.
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param mixed $value
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value)
    {
        // TODO: Implement resolveRouteBinding() method.
    }
}
