<?php

$app->group(['prefix' => 'timemachine', 'namespace' => 'Appkr\Timemachine'], function ($app) {
    $app->get('/', 'TimemachineControllerForLumen@getTimeDiff');
    $app->put('/', 'TimemachineControllerForLumen@setCurrentTime');
    $app->delete('/', 'TimemachineControllerForLumen@resetCurrentTime');
});
