<?php

namespace App\Http\Models;

use App\Http\Caches as Caches;

class Ativo {

    static function paises() {
        $arrDadosDb = Caches::sql("SELECT * FROM sa_pais ORDER BY ds_pais ASC");
        $arrSelect['id_pais'] = '';
        $arrSelect['ds_pais'] = ' --- Selecione --- ';
        array_unshift($arrDadosDb, $arrSelect);

        return $arrDadosDb;
    }

    static function estados($intIdPais) {
        $arrDadosDb = Caches::sql("SELECT * FROM sa_estado WHERE id_pais = " . $intIdPais . " ORDER BY ds_estado ASC");
        $arrSelect['id_estado'] = '';
        $arrSelect['ds_estado'] = ' --- Selecione --- ';
        array_unshift($arrDadosDb, $arrSelect);

        return $arrDadosDb;
    }

    static function cidades($intIdPais, $intIdEstado) {
        $arrDadosDb = Caches::sql("SELECT * FROM sa_cidade WHERE id_pais = " . $intIdPais . " AND id_estado = " . $intIdEstado . " ORDER BY ds_cidade ASC");
        $arrSelect['id_cidade'] = '';
        $arrSelect['ds_cidade'] = ' --- Selecione --- ';
        array_unshift($arrDadosDb, $arrSelect);

        return $arrDadosDb;
    }

}
