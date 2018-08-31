<?php

namespace PragmaRX\Tddd\Package\Data\Repositories\Support;

use PragmaRX\Tddd\Package\Data\Models\Project;
use PragmaRX\Tddd\Package\Data\Models\Test;
use PragmaRX\Tddd\Package\Support\Constants;

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
     * @return \PragmaRX\Tddd\Package\Data\Models\Project|null
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
        $order = "(case
						when state = 'running' then 1
						when state = 'failed' then 2
						when state = 'queued' then 3
						when state = 'ok' then 4
						when state = 'idle' then 5
			        end) asc,

			        updated_at desc";

        $query = Test::select('tddd_tests.*')
                     ->join('tddd_suites', 'tddd_suites.id', '=', 'suite_id')
                     ->orderByRaw($order);

        if ($project_id) {
            $query->where('project_id', $project_id);
        }

        return collect($query->get())->map(function ($test) {
            return $this->getTestInfo($test);
        });
    }

    /**
     * Get all projects.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProjectsAndCacheResult()
    {
        $projects = $this->getProjects();

        app('tddd.cache')->put(Constants::CACHE_PROJECTS_KEY, sha1($projects->toJson()));

        return $projects;
    }

    /**
     * Get all projects.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProjects()
    {
        return Project::all()->map(function ($item) {
            $item['tests'] = $this->getProjectTests($item->id);

            $item['state'] = $this->getProjectState(collect($item['tests']));

            return $item;
        });
    }

    /**
     * Get a SHA1 for all projects.
     *
     * @return boolean
     */
    public function projectSha1HasChanged()
    {
        $currentSha1 = sha1($projectsJson = $this->getProjects()->toJson());

        $oldSha1 = app('tddd.cache')->get(Constants::CACHE_PROJECTS_KEY);

        if ($hasChanged = $currentSha1 != $oldSha1) {
            app('tddd.cache')->put(Constants::CACHE_PROJECTS_KEY, sha1($projectsJson));
        }

        return $hasChanged;
    }

    /**
     * Get a SHA1 for all projects.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProjectsSha1()
    {
        return sha1($this->getProjects()->toJson());
    }

    /**
     * The the project state.
     *
     * @param \Illuminate\Support\Collection $tests
     *
     * @return string
     */
    public function getProjectState($tests)
    {
        if ($tests->contains('state', 'running')) {
            return 'running';
        }

        if ($tests->contains('state', 'queued')) {
            return 'queued';
        }

        if ($tests->contains('state', 'failed')) {
            return 'failed';
        }

        if ($tests->every('state', 'ok')) {
            return 'ok';
        }

        return 'idle';
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
     * @param \PragmaRX\Tddd\Package\Data\Models\Project $project
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

    /**
     * Toggle the enabled state of all projects.
     */
    public function toggleAll()
    {
        Project::all()->each(function ($project) {
            $this->enableProject(!$project->enabled, $project);
        });
    }
}
