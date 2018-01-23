<?php

namespace App\Http\Controllers;

use App\Http\Models\Evento as Evento;
use App\Http\Caches as Caches;

class EventoController extends Controller {

    function valores($intIdEvento) {
        return response()->json('valores kits ' . $intIdEvento);
    }

}
