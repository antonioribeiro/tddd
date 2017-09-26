<?php

Route::group(['prefix' => '/tests'], function() {
    Route::get('/run/all/{project_id}', 'DashboardController@runAll');

    Route::get('/reset/{project_id}', 'DashboardController@reset');

    Route::get('/run/{test_id?}', 'DashboardController@runTest');

    Route::get('/enable/{enable}/{project_id}/{test_id?}', 'DashboardController@enableTests');

    Route::get('/notify/{type}', 'DashboardController@notify');

    Route::get('/{project_id?}', 'DashboardController@allTests');

    Route::get('/{project_id?}', 'DashboardController@allTests');
});

Route::get('/projects', 'DashboardController@allProjects');

Route::get('/dashboard', 'DashboardController@index');

Route::get('/file/open/{filename}/{line?}', 'DashboardController@openFile');
