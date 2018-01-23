<?php

namespace App\Http\Models;

use App\Http\Helpers as Helpers;
use App\Http\Caches as Caches;
use App\Http\Models\Base as Base;
use App\Http\Models\Eventos as Evento;

class Inscritos {

    static function get_inscritos_evento($id_evento) {

        $inscritos_evento = array('return' => 'Nenhum ID de evento repassado ex. /inscritos/{ID_evento}');

        if (empty($id_evento)) {
            $id_evento = Evento::listaIdEventosPorUsuario(app('request')->input('user_id'));
        }

        $arrDadosDb = Caches::sql("CALL proc_dashboard_faturamentos('" . $id_evento . "')");

        return $arrDadosDb;
    }

    static function get_inscritos_modalidade($id_evento) {

        $inscritos_modalidade = array('return' => 'Nenhum ID de evento repassado ex. /inscritos/{ID_evento}/modalidade/{ID_modalidade}');

        if (empty($id_evento)) {
            $id_evento = Evento::listaIdEventosPorUsuario(app('request')->input('user_id'));
        }

        $arrDadosDb = Caches::sql("CALL proc_relatorio_faturamento_modalidades('" . $id_evento . "')");

        return $arrDadosDb;
    }

    static function get_inscritos_categoria($id_evento) {

        $inscritos_categoria = array('return' => 'Nenhum ID de evento repassado ex. /inscritos/{ID_evento}/categoria/ID_categoria');

        if (empty($id_evento)) {
            $id_evento = Evento::listaIdEventosPorUsuario(app('request')->input('user_id'));
        }

        $arrDadosDb = Caches::sql("CALL proc_relatorio_faturamento_categorias('" . $id_evento . "')");

        return $arrDadosDb;
    }

}
