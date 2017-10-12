<?php

namespace PragmaRX\TestsWatcher\Package\Http\Controllers;

class Files extends Controller
{
    /**
     * Open a file in the editor.
     *
     * @param $fileName
     * @param null $suite_id
     * @param null $line
     *
     * @return mixed
     */
    public function editFile($fileName, $suite_id, $line = null)
    {
        $this->executor->exec(
            $command = $this->dataRepository->makeEditFileCommand($fileName, $line, $suite_id)
        );

        return $this->success();
    }

    /**
     * Download an image.
     *
     * @param $filename
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function imageDownload($filename)
    {
        return response()->download(
            base64_decode($filename)
        );
    }
}
