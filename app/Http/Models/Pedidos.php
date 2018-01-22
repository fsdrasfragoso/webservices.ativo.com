<?php

namespace App\Http\Models;

use Illuminate\Support\Facades\DB;
use App\Http\Helpers as Helpers;
use App\Http\Caches as Caches;

class Pedidos {

    static function formaspagamento() {

        $arrDadosDb = Caches::sql("SELECT * FROM sa_formas_pagamento WHERE id_situacao_cadastro = 1 ORDER BY ds_descricao");
        return $arrDadosDb;
    }

    static function status() {

        $arrDadosDb = Caches::sql("SELECT * FROM sa_pedido_status WHERE id_pedido_status in (1,2,5) order by ds_cod");
        return $arrDadosDb;
    }

    static function tipoinscricao() {

        $arrDadosDb = array(array('tipo' => 'SITE', 'id' => 1), array('tipo' => 'BALCÃO', 'id' => 2));
        return $arrDadosDb;
    }

    static function detalhe($id_inscricao) {

        $idEvento = (app('request')->input('evento_id_select') != '') ? app('request')->input('evento_id_select') : 0;
        $idModalidade = (app('request')->input('id_modalidade') != '') ? app('request')->input('id_modalidade') : 0;
        $idCategoria = (app('request')->input('id_categoria') != '') ? app('request')->input('id_categoria') : 0;
        $idFormaPG = (app('request')->input('id_forma_pg') != '') ? app('request')->input('id_forma_pg') : 0;
        $idStatusPedido = (app('request')->input('id_status') != '') ? app('request')->input('id_status') : 0;
        $idLocal = (app('request')->input('id_tipo_inscricao') != '') ? app('request')->input('id_tipo_inscricao') : 0;
        $intDocumento = (app('request')->input('nr_cpf') != '') ? app('request')->input('nr_cpf') : 0;
        $intProtocolo = (app('request')->input('nr_protocolo') != '') ? app('request')->input('nr_protocolo') : 0;
        $intInscricao = $id_inscricao;
        $intLimit = (app('request')->input('qtd_limit') != '') ? app('request')->input('qtd_limit') : 50000;
        $offset = (app('request')->input('offset') != '') ? app('request')->input('offset') : 0;
        $groupBy = 0;

        $arrDadosDb = DB::select("CALL proc_relatorio_inscritos_geral(" . $idEvento . "," . $idModalidade . "," . $idCategoria . "," . $idFormaPG . "," . $idStatusPedido . "," . $idLocal . "," . $intDocumento . "," . $intProtocolo . ", " . $intInscricao . ", " . $groupBy . ", " . $intLimit . ", " . $offset . ")");

        return $arrDadosDb[0];
    }

    static function update() {
        // query do update        
        $returnBanco = DB::table('sa_pedido_evento')
                ->where('id_pedido_evento', app('request')->input('id_inscricao'))
                ->update(array('id_modalidade' => app('request')->input('id_modalidade'), 'id_tamanho_camiseta' => app('request')->input('id_camiseta')));

        // mensagem de retorno padrão
        $arrRetorno['status'] = 'false';
        $arrRetorno['msg'] = 'Erro ao tentar efetuar atualização!';

        if ($returnBanco) {
            $arrRetorno['status'] = 'true';
            $arrRetorno['msg'] = 'Registro atualizado com sucesso!';
        }

        return $arrRetorno;
    }

}
