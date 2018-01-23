<?php

namespace App\Http\Controllers;

use App\Http\Models\Mobile as Mobile;
use App\Http\Caches as Caches;

class MobileController extends Controller {

    function valores($intIdEvento) {
        return response()->json('valores kits ' . $intIdEvento);
    }

}
