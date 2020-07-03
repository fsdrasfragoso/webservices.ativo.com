<?php

namespace App\Http\Controllers;

use App\Http\Models\Evento as Evento;
use App\Http\Caches as Caches;

class EventoController {

    function getById($intIdEvento) {
        $arrDados = Evento::getById($intIdEvento);
        return response()->json($arrDados);
    }

    function calendario() {
        $arrDados = Evento::calendario();
        return response()->json($arrDados);
    }

    function resultados($intIdEvento) {
        $arrDados = Evento::resultados($intIdEvento);
        return response()->json($arrDados);
    }

    function inscritos($intIdEvento) {
        $arrDados = Evento::inscritos($intIdEvento);
        return response()->json($arrDados);
    }

    function fotos($intIdEvento) {
        $arrDados = Evento::fotos($intIdEvento);
        return response()->json($arrDados);
    }

    function lotes($intIdEvento) {
        $arrDados = Evento::lotes($intIdEvento);
        return response()->json($arrDados);
    }

    function modalidades($intIdEvento) {
        $arrDados = Evento::modalidades($intIdEvento);
        return response()->json($arrDados);
    }

    function categorias($intIdEvento) {
        $arrDados = Evento::categorias($intIdEvento);
        return response()->json($arrDados);
    }

    function kits($intIdEvento) {
        $arrDados = Evento::kits($intIdEvento);
        return response()->json($arrDados);
    }

    function valoresKit($intIdEvento, $intIdCategoria) {
        $arrDados = Evento::valoresKit($intIdEvento, $intIdCategoria);
        return response()->json($arrDados);
    }

    function produtos($intIdEvento) {
        $arrDados = Evento::produtos($intIdEvento);
        return response()->json($arrDados);
    }

    function camisetas($intIdEvento) {
        $arrDados = Evento::camisetas($intIdEvento);
        return response()->json($arrDados);
    }

    function certificado($intIdEvento, $intIdNumPeito) {
        $arrDados = Evento::certificado($intIdEvento, $intIdNumPeito);
        return response()->json($arrDados);
    }

    function mcDonaldsGeral() {
        $arrDados = Evento::mcDonalds(null);
        return response()->json($arrDados);
    }

    function mcDonaldsPorEvento($infoIdEvento) {
        $arrDados = Evento::mcDonalds($infoIdEvento);
        return response()->json($arrDados);
    }

    function run99($infoIdEvento){
        $arrDados = Evento::run99($infoIdEvento);
        return response()->json($arrDados); 
    }

    function freedomLogin(){
        $arrDados = Evento::freedomLogin(); 
        return response()->json($arrDados); 
    }

   

}
