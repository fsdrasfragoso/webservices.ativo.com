<?php

namespace App\Http\Controllers;

use App\Http\Models\Ativo as Ativo;
use App\Http\Caches as Caches;

class AtivoController extends Controller {

    function valores($intIdEvento) {
        return response()->json('valores kits ' . $intIdEvento);
    }

}
