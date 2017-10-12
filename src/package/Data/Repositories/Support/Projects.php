<?php

namespace PragmaRX\TestsWatcher\Package\Data\Repositories\Support;

use PragmaRX\TestsWatcher\Package\Entities\Project;
use PragmaRX\TestsWatcher\Package\Entities\Test;

trait Projects
{
    /**
     * Create or update a project.
     *
     * @param $name
     * @param $path
     * @param $tests_path
     *
     * @return static
     */
    public function createOrUpdateProject($name, $path, $tests_path)
    {
        return Project::updateOrCreate(['name' => $name], ['path' => $path, 'tests_path' => $tests_path]);
    }

    /**
     * Find project by id.
     *
     * @return \PragmaRX\TestsWatcher\Package\Entities\Project|null
     */
    public function findProjectById($id)
    {
        return Project::find($id);
    }

    /**
     * Delete unavailable projects.
     *
     * @param $projects
     */
    public function deleteMissingProjects($projects)
    {
        foreach (Project::all() as $project) {
            if (!in_array($project->name, $projects)) {
                $project->delete();
            }
        }
    }

    /**
     * Get all tests.
     *
     * @param null $project_id
     *
     * @return array
     */
    public function getProjectTests($project_id = null)
    {
        $tests = [];

        $order = "(case
						when state = 'running' then 1
						when state = 'failed' then 2
						when state = 'queued' then 3
						when state = 'ok' then 4
						when state = 'idle' then 5
			        end) asc,

			        updated_at desc";

        $query = Test::select('ci_tests.*')
                     ->join('ci_suites', 'ci_suites.id', '=', 'suite_id')
                     ->orderByRaw($order);

        if ($project_id) {
            $query->where('project_id', $project_id);
        }

        foreach ($query->get() as $test) {
            $tests[] = $this->getTestInfo($test);
        }

        return collect($tests);
    }

    /**
     * Get all projects.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getProjects()
    {
        return Project::all()->map(function ($item) {
            if (!isset($item['tests'])) {
                $item['tests'] = [];
            }

            return $item;
        });
    }

    /**
     * Enable tests.
     *
     * @param $enable
     * @param $project_id
     *
     * @return bool
     */
    public function enableProjects($enable, $project_id)
    {
        $enable = $enable === 'true';

        $projects = $project_id == 'all'
            ? Project::all()
            : Project::where('id', $project_id)->get();

        foreach ($projects as $test) {
            $this->enableProject($enable, $test);
        }

        return $enable;
    }

    /**
     * Enable a test.
     *
     * @param $enable
     * @param \PragmaRX\TestsWatcher\Package\Entities\Project $project
     */
    protected function enableProject($enable, $project)
    {
        $project->timestamps = false;

        $project->enabled = $enable;

        $project->save();
    }

    /**
     * Run all tests or projects tests.
     *
     * @param null $project_id
     */
    public function runProjectTests($project_id = null)
    {
        $tests = $this->queryTests($project_id)->get();

        foreach ($tests as $test) {
            $this->enableTest(true, $test);

            // Force test to the queue
            $this->runTest($test, true);
        }
    }

    /**
     * Run all tests or projects tests.
     *
     * @param null $project_id
     */
    public function reset($project_id = null)
    {
        foreach ($this->queryTests($project_id)->get() as $test) {
            $this->resetTest($test);
        }
    }
}
