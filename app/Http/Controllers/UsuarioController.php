<?php

namespace App\Http\Controllers;

use App\Http\Models\Kits as Kits;
use App\Http\Caches as Caches;

class UsuarioController extends Controller {

    function valores($intIdEvento) {
        return response()->json('valores kits ' . $intIdEvento );
    }

}
