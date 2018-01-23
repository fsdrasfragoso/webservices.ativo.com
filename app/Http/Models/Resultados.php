<?php

namespace App\Http\Models;

use Illuminate\Support\Facades\DB;
use App\Http\Helpers as Helpers;
use App\Http\Caches as Caches;

class Resultados {

    static function resultadosPorEvento($id_evento) {
        $arrDadosDb = Caches::sql("SELECT * FROM sa_pedido_status WHERE id_pedido_status in (1,2,5) order by ds_cod");

        return $arrDadosDb;
    }

    static function upload() {
        $id_evento = app('request')->input('id_evento');
        $id_usuario = app('request')->input('id_usuario');
        $exibir_site = app('request')->input('exibir_site');
        $arrDadosResultados = json_decode(html_entity_decode(app('request')->input('json_result')));

        // limpando a tabela para inserir um novo resultado no banco
        DB::table('sa_evento_resultado_participante')->where('id_evento', '=', $id_evento)->delete();

        $arrColunasOutros = array('equipe', 'classificacao_total', 'classificacao_categoria', 'classificacao_sexo', 'velocidade_media');
        $arrColunasMaratona = array('parcial_1_ponto_ctrl', 'parcial_1_temp_acum', 'parcial_1_pace_med_acum', 'parcial_1_temp_liq_trecho', 'parcial_1_pace_med_trecho',
            'parcial_2_ponto_ctrl', 'parcial_2_temp_acum', 'parcial_2_pace_med_acum', 'parcial_2_temp_liq_trecho', 'parcial_2_pace_med_trecho');

        foreach ($arrDadosResultados as $objResultado) {

            // salvando o resultado do usuário
            $idResultadoBanco = DB::table('sa_evento_resultado_participante')->insertGetId(
                    [
                        'id_evento_resultado_participante_aux' => 0,
                        'id_evento' => $id_evento,
                        'nm_usuario' => isset($objResultado->nome_atleta) ? $objResultado->nome_atleta : '',
                        'ds_chipcode' => '',
                        'nr_peito' => isset($objResultado->nr_peito) ? $objResultado->nr_peito : 0,
                        'ds_categoria' => isset($objResultado->categoria) ? $objResultado->categoria : '',
                        'ds_sexo' => isset($objResultado->sexo) ? $objResultado->sexo : 'I',
                        'nr_tempo_total' => isset($objResultado->tempo_total) ? $objResultado->tempo_total : 0,
                        'ds_equipe' => isset($objResultado->equipe) ? $objResultado->equipe : '',
                        'ds_modalidade' => isset($objResultado->modalidade) ? $objResultado->modalidade : '',
                        'ds_cpf' => isset($objResultado->cpf) ? $objResultado->cpf : 0,
                        'id_usuario' => isset($objResultado->id_usuario) ? $objResultado->id_usuario : 0,
                        'nr_percurso' => isset($objResultado->percurso) ? $objResultado->percurso : 0,
                        'dt_criacao' => date('Y-m-d H:i:s'),
                        'nr_pace' => isset($objResultado->pace) ? $objResultado->pace : 0,
                        'id_responsavel' => 0,
                        'nr_tempo_bruto' => isset($objResultado->tempo_bruto) ? $objResultado->tempo_bruto : 0
                    ]
            );

            // salvando os dados do resultados outros
            foreach ($arrColunasOutros as $key => $outrosCampos) {
                $idResultadoOutros = DB::table('sa_evento_resultado_participante_outros')->insertGetId([
                    'id_evento_resultado_participante' => $idResultadoBanco,
                    'ds_outros' => $key,
                    'ds_valor_outros' => isset($objResultado->$outrosCampos) ? $objResultado->$outrosCampos : '',
                    'dt_criacao' => date('Y-m-d H:i:s')
                        ]
                );
            }

            // salvando os dados do resultados outros
            foreach ($arrColunasMaratona as $key => $outrosCampos) {
                if (isset($objResultado->$outrosCampos)) {
                    $idResultadoOutros = DB::table('sa_evento_resultado_participante_outros')->insertGetId([
                        'id_evento_resultado_participante' => $idResultadoBanco,
                        'ds_outros' => $key,
                        'ds_valor_outros' => isset($objResultado->$outrosCampos) ? $objResultado->$outrosCampos : '',
                        'dt_criacao' => date('Y-m-d H:i:s')
                            ]
                    );
                }
            }
        }

        // atualizando a flag para exibir resultado
        $returnBanco = DB::table('sa_evento')->where('id_evento', $id_evento)->update(array('fl_resultado' => 1, 'fl_carrossel' => $exibir_site));

        if (isset($idResultadoOutros)) {
            $arrDadosDb = array('status' => true, 'mensagem' => 'Resultados salvos com sucesso !');
        } else {
            $arrDadosDb = array('status' => false, 'mensagem' => 'Erro ao efetuar o upload do arquivo !');
        }

        return $arrDadosDb;
    }

}
