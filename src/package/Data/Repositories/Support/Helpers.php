<?php

namespace PragmaRX\Tddd\Package\Data\Repositories\Support;

use Symfony\Component\Finder\SplFileInfo;

trait Helpers
{
    protected $ansiConverter;

    /**
     * Carriage return to <br>.
     *
     * @param $lines
     *
     * @return string
     */
    protected function CRToBr($lines)
    {
        return str_replace("\n", '<br>', $lines);
    }

    /**
     * <br> to carriage return.
     *
     * @param $lines
     *
     * @return string
     */
    protected function brToCR($lines)
    {
        return str_replace('<br>', "\n", $lines);
    }

    /**
     * Create link to call the editor for a file.
     *
     * @param $test
     * @param $fileName
     * @param $line
     * @param $occurrence
     *
     * @return string
     */
    protected function createLinkToEditFile($test, $fileName, $line, $occurrence)
    {
        if (!$this->fileExistsOnTest($fileName, $test)) {
            return $line[$occurrence];
        }

        $fileName = base64_encode($fileName);

        $tag = sprintf(
            '<a href="javascript:jQuery.get(\'%s\');" class="file">%s</a>',
            route(
                'tests-watcher.file.edit',
                [
                    'filename' => $fileName,
                    'suite_id' => $test->suite->id,
                    'line'     => $line[2],
                ]
            ),
            $line[$occurrence]
        );

        return $tag;
    }

    /**
     * Create links.
     *
     * @param $lines
     * @param $matches
     *
     * @return mixed
     */
    protected function createLinks($lines, $matches, $test)
    {
        foreach ($matches as $line) {
            if (!empty($line) && is_array($line) && count($line) > 0 && is_array($line[0]) && count($line[0]) > 0) {
                $occurence = strpos($lines, $line[0]) === false ? 1 : 0;

                $lines = str_replace(
                    $line[$occurence],
                    $this->createLinkToEditFile($test, $line[1], $line, $occurence),
                    $lines
                );
            }
        }

        return $lines;
    }

    /**
     * Find source code references.
     *
     * Must find
     *
     *   at Object..test (resources/assets/js/tests/example.spec.js:4:23
     *
     *   (resources/assets/js/tests/example.spec.js:4
     *
     *   /resources/assets/js/tests/example.php:449
     *
     * @param $lines
     * @param $test
     *
     * @return mixed
     */
    protected function findSourceCodeReferences($lines, $test)
    {
        preg_match_all(
            config('tddd.root.regex_file_matcher'),
            strip_tags($this->brToCR($lines)),
            $matches,
            PREG_SET_ORDER
        );

        return array_filter($matches);
    }

    /**
     * @return mixed
     */
    public function getAnsiConverter()
    {
        return $this->ansiConverter;
    }

    /**
     * Get the default editor.
     *
     * @return array
     */
    protected function getDefaultEditor()
    {
        if (is_null($default = collect(config('tddd.editors'))->where('default', true)->first())) {
            die('FATAL ERROR: default editor not configured');
        }

        return $default;
    }

    /**
     * Get the editor command.
     *
     * @param $suite
     *
     * @return string
     */
    protected function getEditorBinary($suite)
    {
        return $this->getEditor($suite)['bin'];
    }

    /**
     * Get the editor.
     *
     * @param $suite
     *
     * @return array
     */
    protected function getEditor($suite)
    {
        if (empty($suite) || is_null($editor = config("tddd.editors.{$suite->editor}"))) {
            return $this->getDefaultEditor();
        }

        return $editor;
    }

    /**
     * Generates data for the javascript client.
     */
    public function getJavascriptClientData()
    {
        $data = [
            'routes' => [
                'prefixes' => config('tddd.routes.prefixes'),
            ],

            'project_id' => request()->get('project_id'),

            'test_id' => request()->get('test_id'),

            'poll_interval' => config('tddd.root.poll_interval'),

            'root' => config('tddd.root'),
        ];

        return json_encode($data);
    }

    /**
     * Get a list of png files to store in database.
     *
     * @param $test
     * @param $log
     *
     * @return null|string
     */
    protected function getScreenshots($test, $log)
    {
        $screenshots = $test->suite->tester->name !== 'dusk'
            ? $this->getOutput($test, $test->suite->tester->output_folder, $test->suite->tester->output_png_fail_extension)
            : $this->parseDuskScreenshots($log, $test->suite->tester->output_folder);

        if (is_null($screenshots)) {
            return;
        }

        return json_encode((array) $screenshots);
    }

    /**
     * Check if the class is abstract.
     *
     * @param $file
     *
     * @return bool
     */
    protected function isAbstractClass($file)
    {
        return (bool) preg_match(
            '/^abstract\s+class[A-Za-z0-9_\s]{1,100}{/im',
            file_get_contents($file)
        );
    }

    /**
     * Check if the file is testable.
     *
     * @param $file
     *
     * @return bool
     */
    protected function isTestable($file)
    {
        return ends_with($file, '.php')
            ? !$this->isAbstractClass($file)
            : true;
    }

    /**
     * Create links for files.
     *
     * @param $lines
     *
     * @return string
     */
    protected function linkFiles($lines, $test)
    {
        $matches = $this->findSourceCodeReferences($lines, $test);

        if (count($matches) != 0) {
            $lines = $this->createLinks($lines, $matches, $test);
        }

        return $this->CRToBr($lines);
    }

    /**
     * @param $test
     *
     * @return string
     */
    protected function makeEditFileUrl($test)
    {
        return route(
            'tests-watcher.file.edit',
            [
                'filename' => base64_encode($test->path.DIRECTORY_SEPARATOR.$test->name),
                'suite_id' => $test->suite->id,
            ]
        );
    }

    /**
     * Generate a lista of screenshots.
     *
     * @param $log
     * @param $folder
     *
     * @return array|null
     */
    protected function parseDuskScreenshots($log, $folder)
    {
        preg_match_all('/([0-9]\)+\s.+::)(.*)/', $log, $matches, PREG_SET_ORDER);

        $result = [];

        foreach ($matches as $line) {
            $name = str_replace("\r", '', $line[2]);

            $result[] = $folder.DIRECTORY_SEPARATOR."failure-{$name}-0.png";
        }

        return count($result) == 0 ? null : $result;
    }

    /**
     * Remove before word.
     *
     * @param $diff
     *
     * @return mixed
     */
    protected function removeBefore($diff)
    {
        return str_replace('before', '', $diff);
    }

    /**
     * Properly render HTML source code.
     *
     * @param string
     *
     * @return string
     */
    protected function renderHtml($contents)
    {
        return nl2br(
            htmlentities($contents)
        );
    }

    /**
     * Check if a path is excluded.
     *
     * @param $exclusions
     * @param $path
     * @param string $file
     *
     * @return bool
     */
    public function isExcluded($exclusions, $path, $file = '')
    {
        if ($file) {
            if (!$file instanceof SplFileInfo) {
                $path = make_path([$path, $file]);
            } else {
                $path = $file->getPathname();
            }
        }

        foreach ($exclusions ?: [] as $excluded) {
            if (starts_with($path, $excluded)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Format output log.
     *
     * @param $log
     *
     * @return mixed|string
     */
    public function formatLog($log, $test)
    {
        return !empty($log)
            ? $this->linkFiles($this->ansi2Html($log), $test)
            : $log;
    }

    /**
     * Convert output ansi chars to html.
     *
     * @param $log
     *
     * @return mixed|string
     */
    protected function ansi2Html($log)
    {
        $string = html_entity_decode(
            $this->ansiConverter->convert($log)
        );

        $string = str_replace("\r\n", '<br>', $string);

        $string = str_replace("\n", '<br>', $string);

        return $string;
    }

    /**
     * Remove ansi codes from string.
     *
     * @param $string
     *
     * @return string
     */
    public function removeAnsiCodes($string)
    {
        return strip_tags(
            $this->ansi2Html($string)
        );
    }

    /**
     * Get the test output.
     *
     * @param $test
     * @param $outputFolder
     * @param $extension
     *
     * @return null|string
     */
    protected function getOutput($test, $outputFolder, $extension)
    {
        if (empty($outputFolder)) {
            return;
        }

        $file = make_path([
            make_path([$test->suite->project->path, $outputFolder]),
            str_replace(['.php', '::', '\\', '/'], ['', '.', '', ''], $test->name).$extension,
        ]);

        return file_exists($file) ? $this->renderHtml(file_get_contents($file)) : null;
    }

    /**
     * Encode a image or html for database storage.
     *
     * @param $file
     *
     * @return bool|mixed|string
     */
    protected function encodeFile($file)
    {
        $type = pathinfo($file, PATHINFO_EXTENSION);

        $data = file_get_contents($file);

        if ($type == 'html') {
            return $data;
        }

        return 'data:image/'.$type.';base64,'.base64_encode($data);
    }

    /**
     * Make open file command.
     *
     * @param $file
     * @param $line
     * @param int $suite_id
     *
     * @return string
     */
    public function makeEditFileCommand($file, $line, $suite_id)
    {
        $suite = $this->findSuiteById($suite_id);

        $file = $this->addProjectRootPath(
            base64_decode($file),
            $suite
        );

        $command = trim(str_replace(
            ['{file}', '{line}'],
            [$file, $line],
            $this->getEditorBinary($suite)
        ));

        return ends_with($command, ':')
            ? substr($command, 0, -1)
            : $command;
    }

    /**
     * Check if a file exists for a particular test.
     *
     * @param $filename
     * @param $test
     *
     * @return bool
     */
    public function fileExistsOnTest($filename, $test)
    {
        return file_exists(
            $this->addProjectRootPath($filename, $test->suite)
        );
    }

    /**
     * Add project root to path.
     *
     * @param $fileName
     * @param $suite
     *
     * @return string
     */
    public function addProjectRootPath($fileName, $suite)
    {
        if (starts_with($fileName, DIRECTORY_SEPARATOR) || empty($suite)) {
            return $fileName;
        }

        return $suite->project->path.DIRECTORY_SEPARATOR.$fileName;
    }

    /**
     * Ansi converter setter.
     *
     * @param mixed $ansiConverter
     */
    public function setAnsiConverter($ansiConverter)
    {
        $this->ansiConverter = $ansiConverter;
    }

    /**
     * Normalize a path removing inconsistences.
     *
     * @param $path
     *
     * @return bool|mixed|string
     */
    public function normalizePath($path)
    {
        $path = trim($path);

        $path = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $path);

        if (ends_with($path, DIRECTORY_SEPARATOR)) {
            $path = substr($path, 0, -1);
        }

        return $path;
    }
}
