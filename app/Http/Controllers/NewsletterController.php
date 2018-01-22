<?php

namespace App\Http\Controllers;

use App\Http\Models\Newsletter as Newsletter;
use App\Http\Caches as Caches;

class NewsletterController extends Controller {

    function valores($intIdEvento) {
        return response()->json('valores kits ' . $intIdEvento );
    }

}
