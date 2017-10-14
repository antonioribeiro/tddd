<?php

namespace PragmaRX\TestsWatcher\Package\Data\Repositories\Support;

use PragmaRX\TestsWatcher\Package\Data\Models\Project;
use PragmaRX\TestsWatcher\Package\Data\Models\Suite;
use PragmaRX\TestsWatcher\Package\Data\Models\Tester;
use Symfony\Component\Finder\Finder;

trait Suites
{
    /**
     * Create or update a suite.
     *
     * @param $name
     * @param $project_id
     * @param $suite_data
     *
     * @return Suite|null|bool
     */
    public function createOrUpdateSuite($name, $project_id, $suite_data)
    {
        $project_id = $project_id instanceof Project ? $project_id->id : $project_id;

        if (is_null($tester = Tester::where('name', $suite_data['tester'])->first())) {
            $this->addMessage('error', "Tester {$suite_data['tester']} not found.");

            return false;
        }

        return Suite::updateOrCreate(
            [
                'name'       => $name,
                'project_id' => $project_id,
            ],
            [
                'tester_id'       => $tester->id,
                'tests_path'      => array_get($suite_data, 'tests_path'),
                'command_options' => array_get($suite_data, 'command_options'),
                'file_mask'       => array_get($suite_data, 'file_mask'),
                'retries'         => array_get($suite_data, 'retries'),
                'editor'          => array_get($suite_data, 'editor'),
            ]
        );
    }

    /**
     * Find suite by project and name.
     *
     * @param $name
     * @param $project_id
     *
     * @return \PragmaRX\TestsWatcher\Package\Data\Models\Suite|null
     */
    public function findSuiteByNameAndProject($name, $project_id)
    {
        return Suite::where('name', $name)
                    ->where('project_id', $project_id)
                    ->first();
    }

    /**
     * Get all suites.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getSuites()
    {
        return Suite::all();
    }

    /**
     * Find suite by id.
     *
     * @return \PragmaRX\TestsWatcher\Package\Data\Models\Suite|null
     */
    public function findSuiteById($id)
    {
        return Suite::find($id);
    }

    /**
     * Remove suites that are not in present in config.
     *
     * @param $suites
     * @param $project
     */
    public function removeMissingSuites($suites, $project)
    {
        Suite::where('project_id', $project->id)->whereNotIn('name', collect($suites)->keys())->each(function ($suite) {
            $suite->delete();
        });
    }

    /**
     * Sync all tests for a particular suite.
     *
     * @param $suite
     * @param $exclusions
     */
    protected function syncSuiteTests($suite, $exclusions)
    {
        $files = $this->getAllFilesFromSuite($suite);

        foreach ($files as $file) {
            if (!$this->isExcluded($exclusions, null, $file) && $this->isTestable($file->getRealPath())) {
                $this->createOrUpdateTest($file, $suite);
            } else {
                // If the test already exists, delete it.
                //
                if ($test = $this->findTestByNameAndSuite($file, $suite)) {
                    $test->delete();
                }
            }
        }

        foreach ($suite->tests as $test) {
            if (!file_exists($path = $test->fullPath)) {
                $test->delete();
            }
        }
    }

    /**
     * Get all files from a suite.
     *
     * @param $suite
     *
     * @return array
     */
    protected function getAllFilesFromSuite($suite)
    {
        if (!file_exists($suite->testsFullPath)) {
            die('FATAL ERROR: directory not found: '.$suite->testsFullPath.'.');
        }

        $files = Finder::create()->files()->in($suite->testsFullPath);

        if ($suite->file_mask) {
            $files->name($suite->file_mask);
        }

        return iterator_to_array($files, false);
    }

    /**
     * Get all suites for a path.
     *
     * @param $path
     *
     * @return mixed
     */
    public function getSuitesForPath($path)
    {
        $projects = $this->getProjects();

        // Reduce the collection of projects by those whose path properties
        // (should be only 1) are contained in the fullpath of our
        // changed file
        $filtered_projects = $projects->filter(function ($project) use ($path) {
            return substr_count($path, $project->path) > 0;
        });

        // Get filtered projects dependencies
        $depends = $projects->filter(function ($project) use ($filtered_projects) {
            if (!is_null($depends = config("ci.projects.{$project->name}.depends"))) {
                return collect($depends)->filter(function ($item) use ($filtered_projects) {
                    return !is_null($filtered_projects->where('name', $item)->first());
                });
            }

            return false;
        });

        // At this point we have (hopefully only 1) project. Now we need
        // the suite(s) associated with the project.
        return Suite::whereIn('project_id', $filtered_projects->merge($depends)->pluck('id'))
                    ->get();
    }
}
