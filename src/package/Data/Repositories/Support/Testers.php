<?php

namespace PragmaRX\TestsWatcher\Package\Data\Repositories\Support;

use PragmaRX\TestsWatcher\Package\Entities\Tester;

trait Testers
{
    /**
     * Create or update a tester.
     *
     * @param $name
     * @param $data
     */
    public function createOrUpdateTester($name, $data)
    {
        Tester::updateOrCreate(
            ['name' => $name],
            [
                'command'                    => $data['command'],
                'output_folder'              => array_get($data, 'output_folder'),
                'output_html_fail_extension' => array_get($data, 'output_html_fail_extension'),
                'output_png_fail_extension'  => array_get($data, 'output_png_fail_extension'),
                'require_tee'                => array_get($data, 'require_tee', false),
                'require_script'             => array_get($data, 'require_script', false),
                'error_pattern'              => array_get($data, 'error_pattern'),
                'env'                        => array_get($data, 'env'),
            ]
        );
    }

    /**
     * Delete unavailable testers.
     *
     * @param $testers
     */
    public function deleteMissingTesters($testers)
    {
        foreach (Tester::all() as $tester) {
            if (!in_array($tester->name, $testers)) {
                $tester->delete();
            }
        }
    }
}
