<?php

namespace PragmaRX\TestsWatcher\Package\Http\Controllers;

use Response;
use PragmaRX\TestsWatcher\Package\Support\Executor;
use PragmaRX\TestsWatcher\Package\Data\Repositories\Data;
use Illuminate\Routing\Controller as IlluminateController;

class Controller extends IlluminateController
{
    /**
     * @var Data
     */
    protected $dataRepository;

    /**
     * @var \PragmaRX\TestsWatcher\Package\Support\Executor
     */
    protected $executor;

    /**
     * DashboardController constructor.
     *
     * @param Data                                            $dataRepository
     * @param \PragmaRX\TestsWatcher\Package\Support\Executor $executor
     */
    public function __construct(Data $dataRepository, Executor $executor)
    {
        $this->dataRepository = $dataRepository;

        $this->executor = $executor;
    }

    /**
     * Return a success response.
     *
     * @param array $result
     *
     * @return mixed
     */
    public function success($result = [])
    {
        return Response::json(array_merge(['success' => true], $result));
    }
}
