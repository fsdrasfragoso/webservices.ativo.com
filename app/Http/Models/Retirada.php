<?php

namespace App\Http\Models;

use App\Http\Caches as Caches;

class Retirada {

    static function proximosEventos() {

        $arrRetorno['status'] = 'error';
        $arrRetorno['dados'] = 'Nenhum retorno para /retirada/proximos-eventos';

        $arrDadosDb = Caches::sql("CALL proc_webservice_retirada_proximos_eventos()");

        if ($arrDadosDb) {
            $arrRetorno['status'] = 'ok';
            $arrRetorno['dados'] = $arrDadosDb;
        }

        return $arrRetorno;
    }

    static function modalidadesEventos($intIdEvento) {

        $arrRetorno['status'] = 'error';
        $arrRetorno['dados'] = 'Nenhum retorno para /retirada/categorias-eventos';

        $arrDadosDb = Caches::sql("CALL proc_webservice_retirada_modalidades_evento(" . $intIdEvento . ")");

        if ($arrDadosDb) {
            $arrRetorno['status'] = 'ok';
            $arrRetorno['dados'] = $arrDadosDb;
        }

        return $arrRetorno;
    }

    static function categoriasEventos($intIdEvento) {
        $arrRetorno['status'] = 'error';
        $arrRetorno['dados'] = 'Nenhum retorno para /retirada/categorias-eventos';

        $arrDadosDb = Caches::sql("CALL proc_webservice_retirada_categorias_evento(" . $intIdEvento . ")");

        if ($arrDadosDb) {
            $arrRetorno['status'] = 'ok';
            $arrRetorno['dados'] = $arrDadosDb;
        }

        return $arrRetorno;
    }

    static function carregarEvento($intIdEvento, $tipo) {

        $arrRetorno['status'] = 'error';
        $arrRetorno['dados'] = 'Nenhum retorno para /retirada/carregar-evento/' . $intIdEvento;

        if (!$intIdEvento) {
            $arrRetorno['status'] = 'error';
            $arrRetorno['dados'] = 'Nenhum ID de usuario repassado ex. /retirada/carregar-evento/{ID_EVENTO}';
        }

        $arrDadosDb = Caches::sql("SELECT id_evento, ds_evento, dt_evento, hr_evento, fl_encerrar_inscricao FROM sa_evento WHERE id_evento = " . $intIdEvento);

        if ($arrDadosDb) {

            $arrDadosDb[0]->modalidades = self::modalidadesEventos($intIdEvento);
            $arrDadosDb[0]->categorias = self::categoriasEventos($intIdEvento);
            $arrDadosDb[0]->camisetas = self::camisetasEvento($intIdEvento);

            $arrDadosDb[0]->inscritos = self::inscritosEvento($intIdEvento, $tipo);
            $arrDadosDb[0]->usuarios = self::usuariosEvento($intIdEvento, $tipo);
            $arrDadosDb[0]->produtos = self::pedidosProdutosEvento($intIdEvento);

            $arrRetorno['status'] = 'ok';
            $arrRetorno['dados'] = $arrDadosDb[0];
        }

        return $arrRetorno;
    }

    static function inscritosEvento($intIdEvento, $tipo) {

        $arrRetorno['status'] = 'error';
        $arrRetorno['dados'] = 'Nenhum retorno para /retirada/inscritos-evento/' . $intIdEvento . '/' . $tipo;

        if (!$intIdEvento) {
            $arrRetorno['status'] = 'error';
            $arrRetorno['dados'] = 'Nenhum ID de evento repassado ex. /retirada/inscritos-evento/{ID_EVENTO}/{TIPO}';
        }

        $arrDadosDb = Caches::sql("CALL proc_webservice_inscritos_evento_tipo_retirada(" . $intIdEvento . ", '" . $tipo . "')");

        if ($arrDadosDb) {
            $arrRetorno['status'] = 'ok';
            $arrRetorno['dados'] = $arrDadosDb;
        }

        return $arrRetorno;
    }

    static function usuariosEvento($intIdEvento, $intTipo) {
        $arrRetorno['status'] = 'error';
        $arrRetorno['dados'] = 'Nenhum retorno para /retirada/usuarios-evento/' . $intIdEvento;

        if (!$intIdEvento) {
            $arrRetorno['status'] = 'error';
            $arrRetorno['dados'] = 'Nenhum ID de evento repassado ex. /retirada/usuarios-evento/{ID_EVENTO}';
        }

        $arrDadosDb = Caches::sql("CALL proc_webservice_usuarios_evento(" . $intIdEvento . ", " . $intTipo . ")");

        if ($arrDadosDb) {
            $arrRetorno['status'] = 'ok';
            $arrRetorno['dados'] = $arrDadosDb;
        }

        return $arrRetorno;
    }

    static function pedidosProdutosEvento($intIdEvento) {
        $arrRetorno['status'] = 'error';
        $arrRetorno['dados'] = 'Nenhum retorno para /retirada/pedidos-produtos-evento/' . $intIdEvento;

        if (!$intIdEvento) {
            $arrRetorno['status'] = 'error';
            $arrRetorno['dados'] = 'Nenhum ID de evento repassado ex. /retirada/pedidos-produtos-evento/{ID_EVENTO}';
        }

        $arrDadosDb = Caches::sql("CALL proc_webservice_produtos_evento(" . $intIdEvento . ")");

        if ($arrDadosDb) {
            $arrRetorno['status'] = 'ok';
            $arrRetorno['dados'] = $arrDadosDb;
        }

        return $arrRetorno;
    }

    static function camisetasEvento($intIdEvento) {
        $arrRetorno['status'] = 'error';
        $arrRetorno['dados'] = 'Nenhum retorno para /retirada/camisetas-evento/' . $intIdEvento;

        if (!$intIdEvento) {
            $arrRetorno['status'] = 'error';
            $arrRetorno['dados'] = 'Nenhum ID de evento repassado ex. /retirada/camisetas-evento/{ID_EVENTO}';
        }

        $arrDadosDb = Caches::sql("CALL proc_webservice_retirada_camisetas_por_evento(" . $intIdEvento . ")");

        if ($arrDadosDb) {
            $arrRetorno['status'] = 'ok';
            $arrRetorno['dados'] = $arrDadosDb;
        }

        return $arrRetorno;
    }

    static function sincrozinarEvento($intIdEvento) {

        var_dump($intIdEvento);
        var_dump($_POST);

        return $arrRetorno;
    }

}
