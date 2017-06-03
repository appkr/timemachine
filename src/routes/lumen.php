<?php

$app->group(['prefix' => 'timemachine', 'namespace' => 'Appkr\Timemachine\Lumen'], function ($app) {
    $app->get('/', 'TimemachineController@getTimeDiff');
    $app->put('/', 'TimemachineController@setCurrentTime');
    $app->delete('/', 'TimemachineController@resetCurrentTime');
});
