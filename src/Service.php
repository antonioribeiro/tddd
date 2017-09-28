<?php

namespace PragmaRX\Ci;

use PragmaRX\TestsWatcher\Services\Base;
use PragmaRX\TestsWatcher\Data\Repositories\Data as DataRepository;

class Service extends Base
{
    protected $dataRepository;

    /**
     * Instantiate a service.
     *
     * @param DataRepository $dataRepository
     */
    public function __construct(DataRepository $dataRepository)
    {
        $this->dataRepository = $dataRepository;
    }

    /**
     * Clear the runs table.
     *
     */
    public function clear()
    {
        $this->dataRepository->clearRuns();
    }
}
