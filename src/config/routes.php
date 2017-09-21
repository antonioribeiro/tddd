<?php

Route::group(['prefix' => '/ci-watcher', 'namespace' => 'PragmaRX\Ci\Vendor\Laravel\Http\Controllers'], function()
{
    Route::get('/tests/run/all/{project_id}', 'DashboardController@runAll');

    Route::get('/tests/run/{test_id?}', 'DashboardController@runTest');

    Route::get('/tests/enable/{enable}/{project_id}/{test_id?}', 'DashboardController@enableTests');

    Route::get('/tests/{project_id?}', 'DashboardController@allTests');

    Route::get('/projects', 'DashboardController@allProjects');

    Route::get('/dashboard', 'DashboardController@index');
});
