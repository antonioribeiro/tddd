<?php

namespace PragmaRX\Tddd\Package\Data\Repositories\Support;

use PragmaRX\Tddd\Package\Data\Models\Tester;

trait Testers
{
    /**
     * Create or update a tester.
     *
     * @param $name
     * @param $data
     */
    public function createOrUpdateTester($data)
    {
        Tester::updateOrCreate(
            ['name' => $data['code']],
            [
                'command'                    => $data['command'],
                'output_folder'              => array_get($data, 'output_folder'),
                'output_html_fail_extension' => array_get($data, 'output_html_fail_extension'),
                'output_png_fail_extension'  => array_get($data, 'output_png_fail_extension'),
                'pipers'                     => array_get($data, 'pipers'),
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
