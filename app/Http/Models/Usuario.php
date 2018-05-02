<?php

namespace App\Http\Models;

use App\Http\Caches as Caches;

class Usuario {

    static function inscricoes($intIdUsuario) {

        $intIdEvento = (app('request')->input('evento') != '') ? app('request')->input('evento') : 0;
        $intLimit = (app('request')->input('qtd') != '') ? app('request')->input('qtd') : 50;
        $intOffset = (app('request')->input('offset') != '') ? app('request')->input('offset') : 0;

        $arrRetorno['status'] = 'error';
        $arrRetorno['dados'] = 'Nenhum retorno para /usuario/inscricoes/' . $intIdUsuario;

        if (!$intIdUsuario) {
            $arrRetorno['status'] = 'error';
            $arrRetorno['dados'] = 'Nenhum ID de usuario repassado ex. /usuario/inscricoes/{ID_USER}';
        }

        $arrDadosDb = Caches::sql("CALL proc_relatorio_inscritos_webservice(" . $intIdEvento . ", " . $intIdUsuario . " , 0, " . $intLimit . ", " . $intOffset . ")");

        if ($arrDadosDb) {
            $arrRetorno['status'] = 'ok';
            $arrRetorno['dados'] = $arrDadosDb;
        }

        return $arrRetorno;
    }


}
