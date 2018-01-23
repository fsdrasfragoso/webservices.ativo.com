<?php

namespace App\Http\Models;

use App\Http\Helpers as Helpers;
use App\Http\Caches as Caches;
use App\Http\Models\Eventos as Evento;

class Relatorios {

    static protected $sql_pagamento = 'SELECT 
	pd.id_pedido, 
	(SELECT ds_descricao FROM sa_pedido_pagamento pg 
	INNER JOIN sa_formas_pagamento fp ON fp.id_formas_pagamento = pg.id_formas_pagamento 
	WHERE pg.id_pedido = pd.id_pedido 
	ORDER BY pg.id_pedido_pagamento 
	DESC LIMIT 1) AS "forma", ps.ds_status
	FROM sa_pedido pd
	INNER JOIN sa_pedido_status ps ON ps.id_pedido_status = pd.id_pedido_status
	INNER JOIN sa_pedido_evento pv ON pv.id_pedido = pd.id_pedido 
	INNER JOIN sa_evento_diretor AS u ON pv.id_evento=u.id_evento';
    static protected $sql_before = " GROUP BY pv.id_pedido_evento";

    static function get_tipos_pagamento($id_evento) {

        $arrDadosDb = array('return' => 'Nenhum ID de evento repassado ex. /pagamentos/{ID_evento}/tipos');

        if (!empty($id_evento)) {
            $arrDadosDb = Caches::sql(self::$sql_pagamento . " 
				WHERE pv.id_evento = " . $id_evento . self::$sql_before);
        } else {

            $id_evento = Evento::listaIdEventosPorUsuario(app('request')->input('user_id'));

            $arrDadosDb = Caches::sql(self::$sql_pagamento . " 
				WHERE pv.id_evento in (" . $id_evento . ")" . self::$sql_before);
        }

        if (!empty($arrDadosDb)) {
            $arrDadosDb = Helpers::order_pagamentos($arrDadosDb);
        }

        return $arrDadosDb;
    }

    static function eventos($id_usuario) {

        $arrDadosDb = array('return' => 'Nenhum ID de usuário repassado ex. /relatorio/eventos/{id_usuario}');
 
      

        if (!empty($id_usuario)) {
            $id_evento = Evento::listaIdEventosPorUsuario($id_usuario);
            
            $arrDadosDb = Caches::sql("CALL proc_extrato_financeiro_vendas_organizador('" . $id_evento . "','APROVADO')");
        }

        return $arrDadosDb;
    }

    static function inscritos() {


        $idEvento = (app('request')->input('evento_id_select') != '') ? app('request')->input('evento_id_select') : 0;
        $idModalidade = (app('request')->input('id_modalidade') != '') ? app('request')->input('id_modalidade') : 0;
        $idCategoria = (app('request')->input('id_categoria') != '') ? app('request')->input('id_categoria') : 0;
        $idFormaPG = (app('request')->input('id_forma_pg') != '') ? app('request')->input('id_forma_pg') : 0;
        $idStatusPedido = (app('request')->input('id_status') != '') ? app('request')->input('id_status') : 0;
        $idLocal = (app('request')->input('id_tipo_inscricao') != '') ? app('request')->input('id_tipo_inscricao') : 0;
        $intDocumento = (app('request')->input('nr_cpf') != '') ? app('request')->input('nr_cpf') : 0;
        $intProtocolo = (app('request')->input('nr_protocolo') != '') ? app('request')->input('nr_protocolo') : 0;
        $intInscricao = (app('request')->input('nr_inscricao') != '') ? app('request')->input('nr_inscricao') : 0;
        $intLimit = (app('request')->input('qtd_limit') != '') ? app('request')->input('qtd_limit') : 50000;
        $offset = (app('request')->input('offset') != '') ? app('request')->input('offset') : 0;
        $groupBy = 0;

        $arrDadosDb = Caches::sql("CALL proc_relatorio_inscritos_geral(" . $idEvento . "," . $idModalidade . "," . $idCategoria . "," . $idFormaPG . "," . $idStatusPedido . "," . $idLocal . "," . $intDocumento . "," . $intProtocolo . ", " . $intInscricao . ", " . $groupBy . ", " . $intLimit . ", " . $offset . ")");

        return $arrDadosDb;
    }

    static function valoresInscricoes($id_evento) {
        $arrRetorno = array('return' => 'Nenhum ID de evento repassado ex. /relatorios/valores-inscricoes/{id_evento}');

        // se não passar um evento, vou buscar as informações de todos os eventos do usuário
        if ($id_evento == 0 && !empty(app('request')->input('user_id'))) {

            $id_evento = Evento::listaIdEventosPorUsuario(app('request')->input('user_id'));
        }


        if (!empty($id_evento)) {
            $arrDadosDb = Caches::sql("SELECT pev.id_evento, COUNT(pev.id_pedido) AS total, SUM(pev.nr_preco + pev.nr_taxa) AS valores, peds.ds_status
                                        FROM sa_pedido_evento AS pev
                                        INNER JOIN sa_usuario AS atleta ON pev.id_usuario = atleta.id_usuario
                                        LEFT JOIN sa_usuario_balcao AS atletaB ON pev.id_usuario_balcao = atletaB.id_usuario
                                        INNER JOIN sa_pedido AS ped ON pev.id_pedido = ped.id_pedido
                                        INNER JOIN sa_pedido_status AS peds ON ped.id_pedido_status = peds.id_pedido_status
                                        WHERE pev.id_evento IN( " . $id_evento . ")
                                        GROUP BY ped.id_pedido_status");

            $arrRetorno = array();
            foreach ($arrDadosDb as $objInfo) {
                $arrRetorno[strtolower($objInfo->ds_status)] = $objInfo->total;
                $arrRetorno[strtolower($objInfo->ds_status . '_total')] = number_format($objInfo->valores, 2, ',', '.');
            }
        }

        return $arrRetorno;
    }

    static function inscritosPorModalidade($id_modalidade) {
        $arrRetorno = array('return' => 'Nenhum ID de evento repassado ex. /relatorios/valores-inscricoes/{id_evento}');

        // se não passar um evento, vou buscar as informações de todos os eventos do usuário
        if ($id_modalidade == 0 && !empty(app('request')->input('user_id'))) {

            $id_evento = Evento::listaIdEventosPorUsuario(app('request')->input('user_id'));
        }

        if (!empty($id_evento)) {
            $arrDadosDb = Caches::sql("SELECT pev.id_evento, COUNT(pev.id_pedido) AS total, SUM(pev.nr_preco + pev.nr_taxa) AS valores, peds.ds_status
                                        FROM sa_pedido_evento AS pev
                                        INNER JOIN sa_usuario AS atleta ON pev.id_usuario = atleta.id_usuario
                                        LEFT JOIN sa_usuario_balcao AS atletaB ON pev.id_usuario_balcao = atletaB.id_usuario
                                        INNER JOIN sa_pedido AS ped ON pev.id_pedido = ped.id_pedido
                                        INNER JOIN sa_pedido_status AS peds ON ped.id_pedido_status = peds.id_pedido_status
                                        WHERE pev.id_evento IN( " . $id_evento . ")
                                        GROUP BY pev.id_evento, ped.id_pedido_status");

            $arrRetorno = array();
            foreach ($arrDadosDb as $objInfo) {
                $arrRetorno[strtolower($objInfo->ds_status)] = $objInfo->total;
                $arrRetorno[strtolower($objInfo->ds_status . '_total')] = number_format($objInfo->valores, 2, ',', '.');
            }
        }

        return $arrRetorno;
    }

    static function inscritosDetalhados($idEvento) {

        $arrDadosDb = Caches::sql("CALL proc_relatorio_inscritos_geral(" . $idEvento . ", 0,0,0,0,0,0,0,0,0, 50000, 0)");
        return $arrDadosDb;
    }

}
