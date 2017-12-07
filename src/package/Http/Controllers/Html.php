<?php

namespace PragmaRX\Tddd\Package\Http\Controllers;

use File;
use Illuminate\Http\Request;

class Html extends Controller
{
    public function view(Request $request)
    {
        File::copyDirectory(
            dirname($index = $request->get('index')),
            public_path(config('tddd.root.coverage.path'))
        );

        return redirect()->to('/'.config('tddd.root.coverage.path').'/'.basename($index));
    }
}
