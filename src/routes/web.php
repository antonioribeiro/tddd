<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/tests'], function () {
    Route::get('/run/all/{project_id}', ['as' => 'tests-watcher.tests.run.all', 'uses' => 'DashboardController@runAll']);

    Route::get('/reset/{project_id}', ['as' => 'tests-watcher.tests.reset', 'uses' => 'DashboardController@reset']);

    Route::get('/run/{test_id?}', ['as' => 'tests-watcher.tests.run', 'uses' => 'DashboardController@runTest']);

    Route::get('/enable/{enable}/{project_id}/{test_id?}', ['as' => 'tests-watcher.tests.enable', 'uses' => 'DashboardController@enableTests']);

    Route::get('/notify/{project_id}', ['as' => 'tests-watcher.tests.notify', 'uses' => 'DashboardController@notify']);

    Route::get('/{project_id?}', ['as' => 'tests-watcher.tests.project', 'uses' => 'DashboardController@allTests']);
});

Route::get('/projects', ['as' => 'tests-watcher.projects', 'uses' => 'DashboardController@allProjects']);

Route::get('/dashboard', ['as' => 'tests-watcher.dashboard', 'uses' => 'DashboardController@index']);

Route::get('/file/edit/{filename}/{suite_id}/{line?}', ['as' => 'tests-watcher.file.edit', 'uses' => 'DashboardController@editFile']);

Route::get('/image/download/{filename}', ['as' => 'tests-watcher.image.download', 'uses' => 'DashboardController@imageDownload']);
