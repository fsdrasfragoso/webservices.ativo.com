<?php

namespace App\Http\Controllers;

use App\Http\Models\Retirada as Retirada;
use App\Http\Caches as Caches;

class RetiradaController {
    /*
      @method Listagem dos próximos Evento
     */

    function proximosEventos() {
        $arrDados = Retirada::proximosEventos();
        return response()->json($arrDados);
    }

    /*
      @method Listagem das Modalidades por Evento
      @param intIdEvento - Id do Evento para retornar os dados
     */

    function modalidadesEventos($intIdEvento) {
        $arrDados = Retirada::modalidadesEventos($intIdEvento);
        return response()->json($arrDados);
    }

    /*
      @method Listagem das Categorias por Evento
      @param intIdEvento - Id do Evento para retornar os dados
     */

    function categoriasEventos($intIdEvento) {
        $arrDados = Retirada::categoriasEventos($intIdEvento);
        return response()->json($arrDados);
    }

    /*
      @method Retorna os dados do Evento
      @param intIdEvento - Id do Evento para retornar os dados
     */

    function carregarEvento($intIdEvento) {
        $arrDados = Retirada::carregarEvento($intIdEvento);
        return response()->json($arrDados);
    }

    /*
      @method Listagem dos Inscritos por Evento e Tipo
      @param intIdEvento - Id do Evento para retornar os dados
      @param tipo - 1 Retirada Individual / 2 Retirada Grupo / 1,2 Retirada Completa
     */

    function inscritosEvento($intIdEvento, $tipo) {
        $arrDados = Retirada::inscritosEvento($intIdEvento, $tipo);
        return response()->json($arrDados);
    }

    /*
      @method Listagem dos Usuários por Evento
      @param intIdEvento - Id do Evento para retornar os dados
     */

    function usuariosEvento($intIdEvento) {
        $arrDados = Retirada::usuariosEvento($intIdEvento);
        return response()->json($arrDados);
    }

    /*
      @method Listagem dos pedidos com Produtos por Evento
      @param intIdEvento - Id do Evento para retornar os dados
     */

    function pedidosProdutosEvento($intIdEvento) {
        $arrDados = Retirada::pedidosProdutosEvento($intIdEvento);
        return response()->json($arrDados);
    }

    /*
      @method Listagem de todas Camisetas por Evento
      @param intIdEvento - Id do Evento para retornar os dados
     */

    function camisetasEvento($intIdEvento) {
        $arrDados = Retirada::camisetasEvento($intIdEvento);
        return response()->json($arrDados);
    }

    /*
      @method Sincronização do Evento
      @param intIdEvento - Id do Evento para atualizar os dados
     */

    function sincrozinarEvento($intIdEvento) {
        $arrDados = Retirada::sincrozinarEvento($intIdEvento);
        return response()->json($arrDados);
    }

}
