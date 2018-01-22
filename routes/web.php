<?php

$app->get('/', function () {
    return 'Web Services Ativo.com';
});

$app->group(['prefix' => 'br'], function () use ($app) {

    // kits 
    $app->group(['prefix' => 'kits'], function () use ($app) {
        $app->get('valores/{id_evento:[0-9]+}', 'KitController@valores');
    });

    
    // eventos 
    $app->group(['prefix' => 'eventos'], function () use ($app) {
        $app->get('getById/{id_evento:[0-9]+}', 'EventoController@getById');
    });


    // mobile
    $app->group(['prefix' => 'mobile'], function () use ($app) {
        $app->get('eventos/', 'MobileController@eventos');
    });
});

