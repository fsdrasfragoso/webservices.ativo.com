<?php

$app->get('/', function () {
    return 'Web Services Ativo.com';
});

$app->group(['prefix' => 'br'], function () use ($app) {

    // configuração 
    $app->group(['prefix' => 'config'], function () use ($app) {
        $app->get('paises', 'AtivoController@paises');
        $app->get('estados/{id_pais:[0-9]+}', 'AtivoController@estados');
        $app->get('cidades/{id_pais:[0-9]+}/{id_estado:[0-9]+}', 'AtivoController@cidades');
    });

    // eventos 
    $app->group(['prefix' => 'evento'], function () use ($app) {
        $app->get('getById/{id_evento:[0-9]+}', 'EventoController@getById');
        $app->get('calendario', 'EventoController@calendario');
        $app->get('resultados', 'EventoController@resultado');
        $app->get('inscritos/{id_evento:[0-9]+}', 'EventoController@inscritos');
        $app->get('fotos/{id_evento:[0-9]+}', 'EventoController@fotos');
        $app->get('lotes/{id_evento:[0-9]+}', 'EventoController@lotes');
        $app->get('modalidades/{id_evento:[0-9]+}', 'EventoController@modalidades');
        $app->get('categorias/{id_evento:[0-9]+}', 'EventoController@categorias');
        $app->get('kits/{id_evento:[0-9]+}/{id_modalidade:[0-9]+}', 'EventoController@kits');
        $app->get('valores-kit/{id_evento:[0-9]+}/{id_kit:[0-9]+}', 'EventoController@valoresKit');
        $app->get('produtos/{id_evento:[0-9]+}', 'EventoController@produtos');
        $app->get('camisetas/{id_evento:[0-9]+}', 'EventoController@camisetas');
    });

    // usuários
    $app->group(['prefix' => 'usuario'], function () use ($app) {
        $app->post('login', 'UsuarioController@login');
        $app->get('minha-conta/{id_user:[0-9]+}', 'UsuarioController@minhaConta');
        $app->post('cadastrar', 'UsuarioController@novoCadastro');
        $app->put('editar/{id_user:[0-9]+}', 'UsuarioController@editarCadastro');
        $app->get('amigos/{id_user:[0-9]+}', 'UsuarioController@amigos');
        $app->post('add-amigo/{id_user:[0-9]+}', 'UsuarioController@addAmigos');
        $app->delete('rem-amigo/{id_user:[0-9]+}', 'UsuarioController@remAmigos');
        $app->get('resultados/{id_user:[0-9]+}', 'UsuarioController@resultados');
        $app->get('inscricoes/{id_user:[0-9]+}', 'UsuarioController@inscricoes');
        $app->get('fotos/{id_user:[0-9]+}', 'UsuarioController@fotos');
    });


    // mobile
    $app->group(['prefix' => 'mobile'], function () use ($app) {
        $app->get('eventos/', 'MobileController@eventos');
    });
});

