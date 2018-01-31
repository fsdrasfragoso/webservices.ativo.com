<?php

namespace App\Http\Controllers;

use App\Http\Models\Mobile as Mobile;
use App\Http\Caches as Caches;

class MobileController{

    function eventos() {
        $arrDados = Mobile::eventos();
        return response()->json($arrDados);
    }

}
