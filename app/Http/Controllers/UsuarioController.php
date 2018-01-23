<?php

namespace App\Http\Controllers;

use App\Http\Models\Kits as Kits;
use App\Http\Caches as Caches;

class UsuarioController {

    function valores($intIdEvento) {
        return response()->json('valores kits ' . $intIdEvento );
    }

}
