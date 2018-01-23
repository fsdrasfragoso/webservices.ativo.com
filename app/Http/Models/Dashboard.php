<?php

namespace App\Http\Models;

use App\Http\Models\Eventos as Eventos;
use App\Http\Helpers as Helpers;
use App\Http\Caches as Caches;

class Dashboard {

    static function dashboardGeral($idEvento) {

        if (!is_numeric($idEvento) || $idEvento == 0) {
            $idEvento = Eventos::listaIdEventosPorUsuario(app('request')->input('user_id'));
        }

        $arrDadosDb = Caches::sql("CALL proc_dashboard_faturamentos('" . $idEvento . "')");

        $arrRetorno = array();
        foreach ($arrDadosDb as $objInfo) {
            $arrRetorno[strtolower($objInfo->status_pagamento)][] = $objInfo;
        }
        return $arrRetorno;
    }

    static function geralPorEvento($idEvento) {

        if (!is_numeric($idEvento) || $idEvento == 0) {
            $idEvento = Eventos::listaIdEventosPorUsuario(app('request')->input('user_id'));
        }

        $arrDadosDb = Caches::sql("CALL proc_dashboard_eventos('" . $idEvento . "')");

        $arrRetorno = array();
        foreach ($arrDadosDb as $objInfo) {
            $arrRetorno[$objInfo->formas_pagamento][strtolower($objInfo->status_pagamento)] = $objInfo->qtd;
        }

        return $arrRetorno;
    }

    static function formasPagamento($idEvento) {

        if (!is_numeric($idEvento) || $idEvento == 0) {
            $idEvento = Eventos::listaIdEventosPorUsuario(app('request')->input('user_id'));
        }

        $arrDadosDb = Caches::sql("CALL proc_dashboard_pagamentos('" . $idEvento . "')");

        $arrRetorno = array();
        foreach ($arrDadosDb as $objInfo) {
            $arrRetorno[$objInfo->formas_pagamento][strtolower($objInfo->status_pagamento)] = $objInfo->qtd;
        }

        return $arrRetorno;
    }

    static function modalidades($idEvento) {

        if (!is_numeric($idEvento) || $idEvento == 0) {
            $idEvento = Eventos::listaIdEventosPorUsuario(app('request')->input('user_id'));
        }

        $arrDadosDb = Caches::sql("CALL proc_dashboard_modalidades('" . $idEvento . "', 0)");

        $arrRetorno = array();
        foreach ($arrDadosDb as $objInfo) {
            $arrRetorno[strtoupper($objInfo->modalidade)][strtolower($objInfo->status_pagamento)] = $objInfo->qtd;
        }

        return $arrRetorno;
    }

    static function categorias($idEvento) {

        if (!is_numeric($idEvento) || $idEvento == 0) {
            $idEvento = Eventos::listaIdEventosPorUsuario(app('request')->input('user_id'));
        }

        $arrDadosDb = Caches::sql("CALL proc_dashboard_categorias('" . $idEvento . "', 0)");

        $arrRetorno = array();
        foreach ($arrDadosDb as $objInfo) {
            $arrRetorno[$objInfo->categoria][strtolower($objInfo->status_pagamento)] = $objInfo->qtd;
        }

        return $arrRetorno;
    }

}
