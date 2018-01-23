<?php

namespace App\Http\Models;

use App\Http\Caches as Caches;

class Ativo {

    static function paises() {
        $arrRetorno['status'] = 'error';
        $arrRetorno['dados'] = 'Nenhum retorno para /config/paises';

        $arrDadosDb = Caches::sql("SELECT * FROM sa_pais WHERE id_situacao_cadastro = 1 ORDER BY ds_pais ASC");

        if ($arrDadosDb) {
            $arrSelect['id_pais'] = '';
            $arrSelect['ds_pais'] = ' --- Selecione --- ';
            array_unshift($arrDadosDb, $arrSelect);
            $arrRetorno['status'] = 'ok';
            $arrRetorno['dados'] = $arrDadosDb;
        }

        return $arrRetorno;
    }

    static function estados($intIdPais) {

        $arrRetorno['status'] = 'error';
        $arrRetorno['dados'] = 'Nenhum retorno para /config/estados/{ID_PAIS}';

        if (!$intIdPais) {
            $arrRetorno['status'] = 'error';
            $arrRetorno['dados'] = 'Nenhum ID de PAIS repassado para /config/estados/{ID_PAIS}';
        }

        $arrDadosDb = Caches::sql("SELECT * FROM sa_estado WHERE id_situacao_cadastro = 1 AND id_pais = " . $intIdPais . " ORDER BY ds_estado ASC");

        if ($arrDadosDb) {
            $arrSelect['id_estado'] = '';
            $arrSelect['ds_estado'] = ' --- Selecione --- ';
            array_unshift($arrDadosDb, $arrSelect);
            $arrRetorno['status'] = 'ok';
            $arrRetorno['dados'] = $arrDadosDb;
        }

        return $arrRetorno;
    }

    static function cidades($intIdPais, $intIdEstado) {

        $arrRetorno['status'] = 'error';
        $arrRetorno['dados'] = 'Nenhum retorno para /config/cidades/{ID_PAIS}/{ID_ESTADO}';

        if (!$intIdPais || !$intIdEstado) {
            $arrRetorno['status'] = 'error';
            $arrRetorno['dados'] = 'Nenhum ID de PAIS ou ESTADO repassado para /config/cidades/{ID_PAIS}/{ID_ESTADO}';
        }

        $arrDadosDb = Caches::sql("SELECT * FROM sa_cidade WHERE id_situacao_cadastro = 1 AND id_pais = " . $intIdPais . " AND id_estado = " . $intIdEstado . " ORDER BY ds_cidade ASC");

        if ($arrDadosDb) {
            $arrSelect['id_cidade'] = '';
            $arrSelect['ds_cidade'] = ' --- Selecione --- ';
            array_unshift($arrDadosDb, $arrSelect);
            $arrRetorno['status'] = 'ok';
            $arrRetorno['dados'] = $arrDadosDb;
        }

        return $arrRetorno;
    }

}
