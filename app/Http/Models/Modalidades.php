<?php

namespace App\Http\Models;

use Illuminate\Support\Facades\DB;
use App\Http\Caches as Caches;

class Modalidades {

    static function detalhes($id_modalidade) {

        $arrDadosDb = array('return' => 'Nenhum ID de evento repassado ex. /modalidade/detalhe/{ID_evento}');

        if (!empty($id_modalidade)) {
            $arrDadosDb = DB::table('sa_evento_modalidade')->where('id_modalidade', $id_modalidade)->first();
        }
        return $arrDadosDb;
    }

    static function update($id_modalidade) {

        // mensagem de retorno padrão
        $arrRetorno['status'] = 'false';
        $arrRetorno['msg'] = 'Erro ao tentar efetuar atualização!';
        
        if ($id_modalidade) {
            // query do update        
            $returnBanco = DB::table('sa_evento_modalidade')
                    ->where('id_modalidade', app('request')->input('id_modalidade'))
                    ->update(array('id_situacao_cadastro' => app('request')->input('id_situacao_cadastro'),
                'ds_horario' => app('request')->input('ds_horario'),
                'nr_inscricoes' => app('request')->input('nr_inscricoes')
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
