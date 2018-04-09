<?php

$app->get('/', function () {
    return 'Web Services Ativo.com (V2.0)';
});

$app->group(['prefix' => 'br'], function () use ($app) {

    // configuração 
    $app->group(['prefix' => 'config'], function () use ($app) {
        // ok
        $app->get('paises', 'AtivoController@paises');
        // ok
        $app->get('estados/{id_pais:[0-9]+}', 'AtivoController@estados');
        // ok
        $app->get('cidades/{id_pais:[0-9]+}/{id_estado:[0-9]+}', 'AtivoController@cidades');
    });

    // eventos 
    $app->group(['prefix' => 'evento'], function () use ($app) {
        // ok
        $app->get('getById/{id_evento:[0-9]+}', 'EventoController@getById');

        $app->get('calendario', 'EventoController@calendario');
        // ok
        $app->get('resultados/{id_evento:[0-9]+}', 'EventoController@resultados');
        // ok        
        $app->get('inscritos/{id_evento:[0-9]+}', 'EventoController@inscritos');
        // ok
        $app->get('fotos/{id_evento:[0-9]+}', 'EventoController@fotos');
        // ok
        $app->get('lotes/{id_evento:[0-9]+}', 'EventoController@lotes');
        // ok
        $app->get('modalidades/{id_evento:[0-9]+}', 'EventoController@modalidades');
        // ok
        $app->get('categorias/{id_evento:[0-9]+}', 'EventoController@categorias');
        // ok
        $app->get('kits/{id_evento:[0-9]+}', 'EventoController@kits');
        // ok
        $app->get('valores-kit/{id_evento:[0-9]+}/{id_kit:[0-9]+}', 'EventoController@valoresKit');
        // ok
        $app->get('produtos/{id_evento:[0-9]+}', 'EventoController@produtos');
        // ok
        $app->get('camisetas/{id_evento:[0-9]+}', 'EventoController@camisetas');
        // ok
        $app->get('certificado/{id_evento:[0-9]+}/{id_peito:[0-9]+}', 'EventoController@certificado');
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
        // ok
        $app->get('inscricoes/{id_user:[0-9]+}', 'UsuarioController@inscricoes');

        $app->get('fotos/{id_user:[0-9]+}', 'UsuarioController@fotos');
    });

    // mobile
    $app->group(['prefix' => 'mobile'], function () use ($app) {
        $app->get('eventos/', 'MobileController@eventos');
    });


    // retirada-kit
    $app->group(['prefix' => 'retirada'], function () use ($app) {

        $app->get('proximos-eventos', 'RetiradaController@proximosEventos');

        $app->get('modalidades-evento/{id_evento:[0-9]+}', 'RetiradaController@modalidadesEventos');

        $app->get('categorias-evento/{id_evento:[0-9]+}', 'RetiradaController@categoriasEventos');

        $app->get('carregar-evento/{id_evento:[0-9]+}', 'RetiradaController@carregarEvento');

        $app->get('inscritos-evento/{id_evento}/{tipo}', 'RetiradaController@inscritosEvento');

        $app->get('usuarios-evento/{id_evento:[0-9]+}', 'RetiradaController@usuariosEvento');

        $app->get('pedidos-produtos-evento/{id_evento:[0-9]+}', 'RetiradaController@pedidosProdutosEvento');

        $app->get('camisetas-evento/{id_evento:[0-9]+}', 'RetiradaController@camisetasEvento');

        $app->post('sincronizar/{id_evento:[0-9]+}', 'RetiradaController@sincrozinarEvento');
    });
});

