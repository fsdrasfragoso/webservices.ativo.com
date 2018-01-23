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

    function resultados() {
        $arrDados = Evento::resultados();
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

    function categorias($intIdEvento, $intIdModalidade) {
        $arrDados = Evento::categorias($intIdEvento, $intIdModalidade);
        return response()->json($arrDados);
    }

    function kits($intIdEvento, $intIdModalidade) {
        $arrDados = Evento::kits($intIdEvento, $intIdModalidade);
        return response()->json($arrDados);
    }

    function valoresKit($intIdEvento) {
        $arrDados = Evento::valoresKit($intIdEvento);
        return response()->json($arrDados);
    }

    function produtos($intIdEvento) {
        $arrDados = Evento::produtos($intIdEvento);
        return response()->json($arrDados);
    }

}
