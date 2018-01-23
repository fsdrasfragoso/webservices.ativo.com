<?php

namespace App\Http\Models;

use App\Http\Caches as Caches;

class Evento {

    static function getById($intIdEvento) {

        $arrRetorno['status'] = 'error';
        $arrRetorno['dados'] = 'Nenhum retorno para /evento/getById/' . $intIdEvento;

        if (!$intIdEvento) {
            $arrRetorno['status'] = 'error';
            $arrRetorno['dados'] = 'Nenhum ID de evento repassado ex. /evento/getById/{ID_evento}';
        }

        $arrDadosDb = Caches::sql("SELECT  tc.ds_tarifa, u.ds_nomecompleto as criado_por, m.ds_nomecompleto as modificado_por, c.id_estado, eve.*, DATE_FORMAT(eve.dt_evento, '%d/%m/%Y') AS dt_evento, 
                DATE_FORMAT(eve.dt_criacao, '%d/%m/%Y') AS dt_criacao,
                DATE_FORMAT(eve.dt_modificacao, '%d/%m/%Y') AS dt_modificacao,
                DATE_FORMAT(eve.dt_inicio_inscricao, '%d/%m/%Y') AS dt_inicio_inscricao,
                DATE_FORMAT(eve.dt_fim_inscricao, '%d/%m/%Y') AS dt_fim_inscricao,
                DATE_FORMAT(eve.data_update, '%d/%m/%Y') AS data_update,
                DATE_FORMAT(eve.data_limite_troca_camiseta, '%d/%m/%Y') AS data_limite_troca_camiseta,
                DATE_FORMAT(eve.dt_exibicao_evento, '%d/%m/%Y') AS dt_exibicao_evento
                FROM sa_evento AS eve                 
                LEFT JOIN sa_usuario AS u ON u.id_usuario = eve.id_criador
                LEFT JOIN sa_usuario AS m ON m.id_usuario = eve.id_modificador
                LEFT JOIN sa_cidade AS c ON c.id_cidade = eve.id_cidade AND c.id_pais = eve.id_pais
                LEFT JOIN sa_tarifa_comodidade tc ON tc.id_tarifa_comodidade = eve.id_tarifa_comodidade
                WHERE eve.id_evento = " . $intIdEvento . " GROUP BY eve.id_evento ORDER BY eve.dt_evento DESC");

        if ($arrDadosDb) {
            $arrRetorno['status'] = 'ok';
            $arrRetorno['dados'] = $arrDadosDb[0];
        }

        return $arrRetorno;
    }

    static function calendario() {
        
    }

    static function resultado() {
        
    }

    static function inscritos($intIdEvento) {
        
    }

    static function fotos($intIdEvento) {
        
    }

    static function lotes($intIdEvento) {
        $arrRetorno['status'] = 'error';
        $arrRetorno['dados'] = 'Nenhum retorno para /evento/lotes/' . $intIdEvento;

        if (!$intIdEvento) {
            $arrRetorno['status'] = 'error';
            $arrRetorno['dados'] = 'Nenhum ID de evento repassado ex. /evento/lotes/{ID_evento}';
        }

        $arrDadosDb = Caches::sql("SELECT id_evento_lote, DATE_FORMAT(dt_limite, '%d/%m/%Y') AS dt_limite, nr_inscricoes, ds_descricao FROM sa_evento_lote WHERE id_evento = " . $intIdEvento);

        if ($arrDadosDb) {
            $arrRetorno['status'] = 'ok';
            $arrRetorno['dados'] = $arrDadosDb;
        }

        return $arrRetorno;
    }

    static function modalidades($intIdEvento) {
        $arrRetorno['status'] = 'error';
        $arrRetorno['dados'] = 'Nenhum retorno para /evento/modalidades/' . $intIdEvento;

        if (!$intIdEvento) {
            $arrRetorno['status'] = 'error';
            $arrRetorno['dados'] = 'Nenhum ID de evento repassado ex. /evento/modalidades/{ID_evento}';
        }

        $arrDadosDb = Caches::sql("SELECT id_modalidade, nm_modalidade, IF ( id_situacao_cadastro = 0, 'Não', 'Sim') AS status,
                                       id_situacao_cadastro, nr_metros, IF(nr_inscricoes = 0, 'Ilimitado', nr_inscricoes) as qtd_limite, nr_inscricoes,
                                       ds_horario, IF (fl_restrito_idade = 0, 'Não', CONCAT( 'Sim - ', nr_restrito_de, ' até ', nr_restrito_ate ) ) AS fl_restrito_idade, getVendasEventoPorModalidade (id_evento, id_modalidade) AS vendas
                                       FROM sa_evento_modalidade                                       
                                       WHERE id_evento = " . $intIdEvento);

        if ($arrDadosDb) {
            $arrRetorno['status'] = 'ok';
            $arrRetorno['dados'] = $arrDadosDb;
        }

        return $arrRetorno;
    }

    static function categorias($intIdEvento) {
        $arrRetorno['status'] = 'error';
        $arrRetorno['dados'] = 'Nenhum retorno para /evento/categorias/' . $intIdEvento;

        if (!$intIdEvento) {
            $arrRetorno['status'] = 'error';
            $arrRetorno['dados'] = 'Nenhum ID de evento repassado ex. /evento/categorias/{ID_evento}';
        }


        $arrDadosDb = Caches::sql("SELECT IF(mc.fl_restrito_idade = 0, 'Não', CONCAT( 'Sim - ', mc.nr_restrito_de, ' até ', mc.nr_restrito_ate)) AS restricao,
                                        mc.fl_restrito_idade, mc.id_categoria, mc.nr_restrito_de, mc.nr_restrito_ate, mc.ds_imagem_kit, mc.fl_gratuito, 
                                        mc.fl_camiseta, mc.id_modalidade, mc.ds_categoria, mc.fl_exibir_site, mc.dt_inicio, mc.dt_final,
                                        mc.fl_sexo, mc.fl_permite_inscricao_amigo, mc.ds_kit, mc.nr_quantidade_inscricao, em.ds_modalidade,
                                        getVendasEventoPorCategoria (id_evento, id_categoria) AS vendas,
                                        IF(mc.id_tipo_categoria = 1, 'Individual', 'Revezamento') AS tipo_categoria                                       
                                        FROM sa_modalidade_categoria mc
                                        INNER JOIN sa_evento_modalidade em ON em.id_modalidade = mc.id_modalidade
                                        WHERE em.id_evento = " . $intIdEvento);

        if ($arrDadosDb) {
            $arrRetorno['status'] = 'ok';
            $arrRetorno['dados'] = $arrDadosDb;
        }

        return $arrRetorno;
    }

    static function kits($intIdEvento) {
        
    }

    static function valoresKits($intIdEvento) {
        
    }

    static function produtos($intIdEvento) {

        $arrRetorno['status'] = 'error';
        $arrRetorno['dados'] = 'Nenhum retorno para /evento/produtos/' . $intIdEvento;

        if (!$intIdEvento) {
            $arrRetorno['status'] = 'error';
            $arrRetorno['dados'] = 'Nenhum ID de evento repassado ex. /evento/produtos/{ID_evento}';
        }


        $arrDadosDb = Caches::sql("SELECT
                                            p.id_produto, pc.id_recurso_caracteristica, pr.id_produto_recurso, p.ds_titulo, DATE_FORMAT(p.dt_inicio_exibicao, '%d/%m/%Y') as dt_inicio_exibicao, DATE_FORMAT(p.dt_termino_exibicao, '%d/%m/%Y') as dt_termino_exibicao,
                                             format(p.nr_preco, 2, 'pt_BR') AS nr_preco , pr.nr_quantidade, pc.ds_caracteristica, pc.ds_cor,
                                            (SELECT COUNT(*) as total FROM sa_pedido_produto pp
                                            INNER JOIN sa_pedido p ON p.id_pedido = pp.id_pedido
                                            WHERE pp.id_produto = p.id_produto AND p.id_pedido_status = 2) as nr_vendas
                                        FROM
                                            sa_produto p
                                        INNER JOIN sa_produto_evento pe ON p.id_produto = pe.id_produto
                                        LEFT JOIN sa_produto_recurso pr ON pr.id_produto = p.id_produto
                                        LEFT JOIN sa_recurso_caracteristica AS pc ON (
                                                pr.id_recurso_1 = pc.id_recurso_caracteristica
                                                OR pr.id_recurso_2 = pc.id_recurso_caracteristica
                                        )
                                        WHERE pe.id_evento = " . $intIdEvento . " 
                                        GROUP by pc.id_recurso_caracteristica");

        if ($arrDadosDb) {
            $arrInfo = array();
            foreach ($arrDadosDb as $objInfo) {
                $arrInfo[$objInfo->id_produto]['id_produto'] = $objInfo->id_produto;
                $arrInfo[$objInfo->id_produto]['ds_titulo'] = $objInfo->ds_titulo;
                $arrInfo[$objInfo->id_produto]['dt_inicio_exibicao'] = $objInfo->dt_inicio_exibicao;
                $arrInfo[$objInfo->id_produto]['dt_termino_exibicao'] = $objInfo->dt_termino_exibicao;
                $arrInfo[$objInfo->id_produto]['nr_preco'] = $objInfo->nr_preco;
                $arrInfo[$objInfo->id_produto]['nr_quantidade'] = $objInfo->nr_quantidade;
                $arrInfo[$objInfo->id_produto]['nr_vendas'] = $objInfo->nr_vendas;
                $arrInfo[$objInfo->id_produto]['nr_estoque'] = $objInfo->nr_quantidade - $objInfo->nr_vendas;
                $arrInfo[$objInfo->id_produto]['ds_caracteristica'][] = $objInfo->ds_caracteristica;
            }


            $arrRetorno['status'] = 'ok';
            $arrRetorno['dados'] = $arrInfo;
        }

        return $arrRetorno;
    }

    static function camisetas($intIdEvento) {
        
    }

}
