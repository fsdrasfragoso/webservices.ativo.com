<?php

namespace App\Http;

use Illuminate\Support\Facades\Cache as Cache;

class Caches {

    public static function sql($sql_key) {

        return app('db')->select($sql_key);
        
        $object = Cache::get(md5($sql_key));

        if (!empty(app('request')->input('cache'))) {
            $object = 0;
        }

        if (empty($object)) {
            Cache::forget(md5($sql_key));
            $object = app('db')->select($sql_key);
            Cache::put(md5($sql_key), $object, 10); //10 minutos para tudo
        }

        return $object;
    }

}
