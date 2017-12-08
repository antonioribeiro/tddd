<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => config('tddd.routes.prefixes.dashboard')], function () {
    Route::get('/', ['as' => 'tests-watcher.dashboard', 'uses' => 'Dashboard@index']);

    Route::get('/data', ['as' => 'tests-watcher.dashboard.data', 'uses' => 'Dashboard@data']);
});

Route::group(['prefix' => config('tddd.routes.prefixes.tests')], function () {
    Route::get('/reset/{project_id}', ['as' => 'tests-watcher.tests.reset', 'uses' => 'Tests@reset']);

    Route::get('/run/{test_id?}', ['as' => 'tests-watcher.tests.run', 'uses' => 'Tests@run']);

    Route::get('/{project_id}/{test_id}/enable/{enable}', ['as' => 'tests-watcher.tests.enable', 'uses' => 'Tests@enable']);
});

Route::group(['prefix' => config('tddd.routes.prefixes.projects')], function () {
    Route::get('/{project_id}/enable/{enable}', ['as' => 'tests-watcher.projects.enable', 'uses' => 'Projects@enable']);

    Route::get('/{project_id}/notify', ['as' => 'tests-watcher.tests.notify', 'uses' => 'Projects@notify']);

    Route::post('/reset', ['as' => 'tests-watcher.projects.reset', 'uses' => 'Projects@reset']);

    Route::post('/run', ['as' => 'tests-watcher.projects.run.all', 'uses' => 'Projects@run']);

    Route::get('/toggle-all', ['as' => 'tests-watcher.projects.toggle-all', 'uses' => 'Projects@toggleAll']);
});

Route::group(['prefix' => config('tddd.routes.prefixes.files')], function () {
    Route::get('/edit/{filename}/{suite_id}/{line?}', ['as' => 'tests-watcher.file.edit', 'uses' => 'Files@editFile']);

    Route::get('/{filename}/download', ['as' => 'tests-watcher.image.download', 'uses' => 'Files@imageDownload']);
});

Route::group(['prefix' => config('tddd.routes.prefixes.html')], function () {
    Route::get('/', ['as' => 'tests-watcher.html.view', 'uses' => 'Html@view']);
});
