<?php

namespace App\Http\Controllers;

use App\Http\Models\Ativo as Ativo;
use App\Http\Caches as Caches;

class AtivoController {

    function paises() {
        $arrDados = Ativo::paises();
        return response()->json($arrDados);
    }

    function estados($intIdPais) {
        $arrDados = Ativo::estados($intIdPais);
        return response()->json($arrDados);
    }

    function cidades($intIdPais, $intIdEstado) {
        $arrDados = Ativo::cidades($intIdPais, $intIdEstado);
        return response()->json($arrDados);
    }

}
