<?php

namespace App\Http\Models;

use Illuminate\Support\Facades\DB;
use App\Http\Helpers as Helpers;
use App\Http\Caches as Caches;
use App\Http\Models\Eventos as Evento;

class Financeiro {

    static function extrato($idEvento = 0) {

        $idEventoParam = (app('request')->input('evento_id_select') != '') ? app('request')->input('evento_id_select') : 0;
        $dataDe = (app('request')->input('strDataDe') != '' && app('request')->input('strDataDe') != 0 ) ? Helpers::formatDataBanco(app('request')->input('strDataDe')) : 0;
        $dataAte = (app('request')->input('strDataAte') != '' && app('request')->input('strDataAte') != 0) ? Helpers::formatDataBanco(app('request')->input('strDataAte')) : 0;

        if ($idEventoParam > 0) {
            $idEvento = $idEventoParam;
        }

        if ($idEvento == 0) {
            return array();
        }

        $arrDadosDb = Caches::sql("CALL proc_extrato_financeiro_organizador(" . $idEvento . ",'APROVADO', '" . $dataDe . "', '" . $dataAte . "')");

        $arrRetorno = array();
        foreach ($arrDadosDb as $objInfo) {
            $arrRetorno[$objInfo->mes_ano_pagamento][] = $objInfo;
        }

        return $arrRetorno;
    }

    static function saldoExtrato($idEvento = 0) {
        $idEventoParam = (app('request')->input('evento_id_select') != '') ? app('request')->input('evento_id_select') : 0;
        $dataDe = (app('request')->input('strDataDe') != '' && app('request')->input('strDataDe') != 0 ) ? Helpers::formatDataBanco(app('request')->input('strDataDe')) : 0;
        $dataAte = (app('request')->input('strDataAte') != '' && app('request')->input('strDataAte') != 0) ? Helpers::formatDataBanco(app('request')->input('strDataAte')) : 0;

        if ($idEventoParam > 0) {
            $idEvento = $idEventoParam;
        }

        if ($idEvento == 0) {
            return array();
        }

        $arrDadosDb = Caches::sql("CALL proc_extrato_financeiro_repasse_organizador(" . $idEvento . ",'" . env('TARIFA') . "', '1,2')");

        return $arrDadosDb[0];
    }

    static function lancamentosFuturo($idEvento = 0) {
        $idEventoParam = (app('request')->input('evento_id_select') != '') ? app('request')->input('evento_id_select') : 0;
        $dataDe = (app('request')->input('strDataDe') != '' && app('request')->input('strDataDe') != 0 ) ? Helpers::formatDataBanco(app('request')->input('strDataDe')) : 0;
        $dataAte = (app('request')->input('strDataAte') != '' && app('request')->input('strDataAte') != 0) ? Helpers::formatDataBanco(app('request')->input('strDataAte')) : 0;

        if ($idEventoParam > 0) {
            $idEvento = $idEventoParam;
        }

        if ($idEvento == 0) {
            return array();
        }

        $arrDadosDb = Caches::sql("CALL proc_extrato_financeiro_organizador(" . $idEvento . ",'FUTURO', '" . $dataDe . "', '" . $dataAte . "')");

        $arrRetorno = array();
        foreach ($arrDadosDb as $objInfo) {
            $arrRetorno[$objInfo->mes_liberacao][] = $objInfo;
        }

        return $arrRetorno;
    }

    static function saldoFuturo($idEvento = 0) {
        $idEventoParam = (app('request')->input('evento_id_select') != '') ? app('request')->input('evento_id_select') : 0;
        $dataDe = (app('request')->input('strDataDe') != '' && app('request')->input('strDataDe') != 0 ) ? Helpers::formatDataBanco(app('request')->input('strDataDe')) : 0;
        $dataAte = (app('request')->input('strDataAte') != '' && app('request')->input('strDataAte') != 0) ? Helpers::formatDataBanco(app('request')->input('strDataAte')) : 0;

        if ($idEventoParam > 0) {
            $idEvento = $idEventoParam;
        }

        if ($idEvento == 0) {
            return array();
        }

        $arrDadosDb = Caches::sql("CALL proc_extrato_financeiro_repasse_organizador(" . $idEvento . ",'" . env('TARIFA') . "', '2')");

        return $arrDadosDb[0];
    }

    static function contas($id_evento, $id_user) {
        $arrDadosDb = Caches::sql("SELECT cb.* FROM sa_evento_diretor_conta_bancaria cb
                                    INNER JOIN sa_evento_diretor ed ON ed.id_evento_diretor = cb.id_evento_diretor
                                    WHERE ed.id_evento = " . $id_evento . " AND ed.id_usuario = " . $id_user);

        return $arrDadosDb;
    }

    static function detalheConta($id_conta, $id_user) {
        $arrDadosDb = Caches::sql("SELECT cb.* FROM sa_evento_diretor_conta_bancaria cb
                                    INNER JOIN sa_evento_diretor ed ON ed.id_evento_diretor = cb.id_evento_diretor
                                    WHERE cb.id_evento_diretor_conta_bancaria = " . $id_conta . " AND ed.id_usuario = " . $id_user);

        return $arrDadosDb[0];
    }

    /* adiantamento tipo = 1 */

    static function adiantamento() {
        return self::solicitarSaque(1, env('TARIFA'));
    }

    static function historicoAdiantamento($id_evento) {
        return self::historicoSaques(1, $id_evento);
    }

    static function saqueAdiantamento($id_evento) {
        return self::valorSaques(1, $id_evento);
    }

    /* antecipacao tipo = 2 */

    static function antecipacao() {
        $tarifaSaque = (Helpers::formatValorCalc(app('request')->input('num_valor')) * 2.8 / 100) + env('TARIFA');

        return self::solicitarSaque(2, $tarifaSaque);
    }

    static function historicoAntecipacao($id_evento) {
        return self::historicoSaques(2, $id_evento);
    }

    static function saqueAntecipacao($id_evento) {
        return self::valorSaques(2, $id_evento);
    }

    static function valorSaques($intTipo, $id_evento) {
        $arrDadosDb = DB::select("SELECT format(SUM(es.valor_bruto),2,'de_DE') as saques
                                FROM sa_evento_diretor_saques es
                                INNER JOIN sa_evento_diretor_conta_bancaria cb ON cb.id_evento_diretor_conta_bancaria = es.id_conta_bancaria
                                INNER JOIN sa_evento_diretor ed ON ed.id_evento_diretor = cb.id_evento_diretor
                                WHERE es.tipo = " . $intTipo . " AND ed.id_evento =" . $id_evento . " GROUP BY ed.id_evento ORDER BY es.data_solicitacao DESC");

        if ($arrDadosDb) {
            return $arrDadosDb[0]->saques;
        } else {
            return 0;
        }
    }

    static function historicoSaques($intTipo, $id_evento) {
        $arrDadosDb = DB::select("SELECT DATE_FORMAT( es.data_solicitacao, '%d/%m/%Y' ) AS data, format(es.valor_bruto,2,'de_DE') as valor, format(es.valor_tarifa,2,'de_DE') as tarifa, format(es.valor_liquido,2,'de_DE') as valor_liquido, es.status, cb.banco, cb.agencia, cb.conta
                                FROM sa_evento_diretor_saques es
                                INNER JOIN sa_evento_diretor_conta_bancaria cb ON cb.id_evento_diretor_conta_bancaria = es.id_conta_bancaria
                                INNER JOIN sa_evento_diretor ed ON ed.id_evento_diretor = cb.id_evento_diretor
                                WHERE es.tipo = " . $intTipo . " AND ed.id_evento =" . $id_evento . " ORDER BY es.data_solicitacao DESC");
        return $arrDadosDb;
    }

    static function solicitarSaque($intTipo, $tarifa) {
        // busco o valor máximo que ele pode sacar  de acordo com extrato ou futuro
        if ($tarifa == env('TARIFA')) {
            $arrSaldos = self::saldoExtrato(app('request')->input('id_evento'));
        } else {
            $arrSaldos = self::saldoFuturo(app('request')->input('id_evento'));
        }

        if (Helpers::formatValorCalc(app('request')->input('num_valor')) > 0 && Helpers::formatValorCalc(app('request')->input('num_valor')) <= Helpers::formatValorCalc($arrSaldos->repasse_liquido)) {
            $returnBanco = DB::table('sa_evento_diretor_saques')->insert(
                    array('valor_liquido' => Helpers::formatValorCalc(app('request')->input('num_valor')),
                        'valor_tarifa' => $tarifa,
                        'valor_bruto' => Helpers::formatValorCalc(app('request')->input('num_valor')) + $tarifa,
                        'id_conta_bancaria' => app('request')->input('id_conta'),
                        'data_solicitacao' => date('Y-m-d h:i:s'),
                        'tipo' => $intTipo,
                        'status' => 'Solicitado',
                        'usuario_solicitante' => app('request')->input('user_id')
                    )
            );

            if ($returnBanco) {
                $arrDadosDb['status'] = 'true';
                $arrDadosDb['msg'] = 'Solicitação efetuada com sucesso!';
            } else {
                $arrDadosDb['status'] = 'false';
                $arrDadosDb['msg'] = 'Erro ao tentar efetuar solicitação!';
            }
        } else {
            $arrDadosDb['status'] = 'false';
            $arrDadosDb['msg'] = 'Erro ao tentar efetuar solicitação!';
        }

        return $arrDadosDb;
    }

    static function inscritosDetalhados($idEvento) {
        $arrDadosDb = Caches::sql("CALL relatorio_inscritos_geral(" . $idEvento . ", 0, 0, 0, 0, 0, 0, 0, 50000, 0)");
        return $arrDadosDb;
    }

    static function fechamentoFinanceiroInscricoes($idEvento) {
        $arrDadosDb = Caches::sql("CALL proc_relatorio_faturamento_pagas(" . $idEvento . ")");
        return $arrDadosDb;
    }

    static function fechamentoFinanceiroTicket($idEvento) {
        $arrDadosDb = Caches::sql("CALL proc_relatorio_faturamento_tickets(" . $idEvento . ")");
        return $arrDadosDb;
    }

    static function fechamentoFinanceiroFaturamento($idEvento) {
        $arrDadosDb = Caches::sql("CALL proc_relatorio_faturamento(" . $idEvento . ")");
        return $arrDadosDb;
    }

    static function fechamentoFinanceiroModalidade($idEvento) {
        $arrDadosDb = Caches::sql("CALL proc_relatorio_faturamento_modalidades(" . $idEvento . ")");
        return $arrDadosDb;
    }

    static function fechamentoFinanceiroCategoria($idEvento) {
        $arrDadosDb = Caches::sql("CALL proc_relatorio_faturamento_categorias(" . $idEvento . ")");
        return $arrDadosDb;
    }

    static function fechamentoFinanceiroCanais($idEvento) {
        $arrDadosDb = Caches::sql("CALL proc_relatorio_faturamento_canais(" . $idEvento . ")");
        return $arrDadosDb;
    }

}
