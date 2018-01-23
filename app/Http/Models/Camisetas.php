<?php

namespace App\Http\Models;

use Illuminate\Support\Facades\DB;
use App\Http\Caches as Caches;

class Camisetas {

    static function detalhes($id_camiseta) {

        $arrDadosDb[0] = array('return' => 'Nenhum ID de camiseta repassado ex. /camiseta/detalhe/{id_categoria}');

        if (!empty($id_camiseta)) {
            $arrDadosDb = DB::select("SELECT
                                            ec.id_evento_camiseta, ec.id_evento, ec.id_tamanho_camiseta, ec.nr_cadastrada,
                                            ec.nr_quantidade, ec.fl_sexo, ev.nm_modalidade, ev.ds_modalidade,
                                            IF (ec.id_modalidade,ec.id_modalidade, 0 ) AS id_modalidade,
                                            ( SELECT COUNT(*) AS total
                                                FROM sa_pedido_evento AS pv
                                                INNER JOIN sa_pedido AS pd ON pd.id_pedido = pv.id_pedido
                                                WHERE
                                                        pd.id_pedido_status IN (1, 2, 3, 4)
                                                AND pv.id_evento = ec.id_evento
                                                AND pv.id_tamanho_camiseta = ec.id_tamanho_camiseta
                                            ) AS vendidas,
                                            tc.ds_tamanho
                                        FROM
                                            sa_evento_camiseta AS ec
                                        LEFT JOIN sa_evento_modalidade AS ev ON ev.id_modalidade = ec.id_modalidade
                                        INNER JOIN sa_tamanho_camiseta AS tc ON tc.id_tamanho_camiseta = ec.id_tamanho_camiseta
                                        WHERE ec.id_evento_camiseta = " . $id_camiseta . "
                                        ORDER BY ev.ds_modalidade ASC");
        }
        return $arrDadosDb[0];
    }

    static function tamanhos() {
        $arrDadosDb = DB::select("SELECT id_tamanho_camiseta, ds_tamanho FROM sa_tamanho_camiseta WHERE id_situacao_cadastro = 1");

        return $arrDadosDb;
    }

    static function update($id_camiseta) {
        
        if ($id_camiseta) {
            // query do update        
            $returnBanco = DB::table('sa_evento_camiseta')
                    ->where('id_evento_camiseta', app('request')->input('id_camiseta'))
                    ->update(array('id_modalidade' => app('request')->input('id_modalidade'),
                'id_tamanho_camiseta' => app('request')->input('id_tamanho'),
                'nr_cadastrada' => app('request')->input('nr_cadastrada'),
                'fl_sexo' => app('request')->input('fl_sexo')
                    )
            );
        }

        // mensagem de retorno padrão
        $arrRetorno['status'] = 'false';
        $arrRetorno['msg'] = 'Erro ao tentar efetuar atualização!';

        if ($returnBanco) {
            $arrRetorno['status'] = 'true';
            $arrRetorno['msg'] = 'Registro atualizado com sucesso!';
        }

        return ($arrRetorno);
    }

}
