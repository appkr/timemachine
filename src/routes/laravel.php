<?php

Route::group(['prefix' => 'timemachine', 'namespace' => 'Appkr\Timemachine\Laravel'], function () {
    Route::get('/', 'TimemachineController@getTimeDiff');
    Route::put('/', 'TimemachineController@setCurrentTime');
    Route::delete('/', 'TimemachineController@resetCurrentTime');
});
