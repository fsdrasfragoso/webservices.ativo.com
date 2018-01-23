<?php

namespace App\Http\Models;

use Illuminate\Support\Facades\DB;
use \App\Http\Helpers as Helpers;
use App\Http\Caches as Caches;

class Categorias {

    static function detalhes($id_categoria) {

        $arrDadosDb[0] = array('return' => 'Nenhum ID de categoria repassado ex. /categoria/detalhe/{id_categoria}');

        if (!empty($id_categoria)) {
            $arrDadosDb = DB::select("SELECT em.id_evento, mc.id_tipo_categoria, IF(mc.fl_restrito_idade = 0, 'Não', CONCAT( 'Sim - ', mc.nr_restrito_de, ' até ', mc.nr_restrito_ate)) AS restricao, IF(mc.fl_camiseta = 0, 'Não', 'Sim') AS camiseta,
                mc.fl_restrito_idade, mc.id_categoria, mc.nr_restrito_de, mc.nr_restrito_ate, mc.ds_imagem_kit, mc.fl_gratuito, 
                mc.fl_camiseta, mc.id_modalidade, mc.ds_categoria, mc.fl_exibir_site, DATE_FORMAT(mc.dt_inicio, '%d/%m/%Y') AS dt_inicio, DATE_FORMAT(mc.dt_final, '%d/%m/%Y') AS dt_final,
                mc.fl_sexo, mc.fl_permite_inscricao_amigo, mc.ds_kit, mc.nr_quantidade_inscricao, em.ds_modalidade,
                getVendasEventoPorCategoria (id_evento, id_categoria) AS vendas,
                IF(mc.id_tipo_categoria = 1, 'Individual', 'Revezamento') AS tipo_categoria,
                IF(mc.fl_gratuito = 1, 'Sim', 'Não') AS gratuito
                FROM sa_modalidade_categoria mc
                INNER JOIN sa_evento_modalidade em ON em.id_modalidade = mc.id_modalidade
                WHERE mc.id_categoria = " . $id_categoria);
        }

        return $arrDadosDb[0];
    }

    static function imagens($id_categoria, $id_evento) {

        $arrDadosDb = DB::select("SELECT id_evento_imagem, CONCAT('https://checkout.akamaized.net/', ds_url_imagem, '/',ds_imagem) as url FROM sa_evento_imagem_s3 where id_categoria = " . $id_categoria . " AND id_evento = " . $id_evento);

        return $arrDadosDb;
    }

    static function update($id_categoria) {

        // mensagem de retorno padrão
        $arrRetorno['status'] = 'false';
        $arrRetorno['msg'] = 'Erro ao tentar efetuar atualização!';

        if ($id_categoria) {
            // query do update        
            $returnBanco = DB::table('sa_modalidade_categoria')
                    ->where('id_categoria', app('request')->input('id_categoria'))
                    ->update(array('fl_exibir_site' => app('request')->input('fl_exibir_site'),
                'nr_quantidade_inscricao' => app('request')->input('nr_quantidade_inscricao'),
                'fl_sexo' => app('request')->input('fl_sexo'),
                'dt_inicio' => Helpers::formatDataBanco(app('request')->input('dt_inicio')),
                'dt_final' => Helpers::formatDataBanco(app('request')->input('dt_final')),
                'fl_permite_inscricao_amigo' => app('request')->input('fl_permite_inscricao_amigo'),
                'id_tipo_categoria' => app('request')->input('id_tipo_categoria'),
                'ds_kit' => app('request')->input('ds_kit')
                    )
            );
        }

        if ($returnBanco) {
            $arrRetorno['status'] = 'true';
            $arrRetorno['msg'] = 'Registro atualizado com sucesso!';
        }

        return ($arrRetorno);
    }

}
