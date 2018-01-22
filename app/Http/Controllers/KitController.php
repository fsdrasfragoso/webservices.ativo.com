<?php

namespace App\Http\Controllers;

use App\Http\Models\Kit as Kit;
use App\Http\Caches as Caches;

class KitController extends Controller {

    function valores($intIdEvento) {
        return response()->json('valores kits ' . $intIdEvento);
    }

}
