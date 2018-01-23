<?php

namespace App\Http\Models;

use Illuminate\Support\Facades\DB;
use App\Http\Caches as Caches;
use \App\Http\Helpers as Helpers;

/**
 * Description of Eventos
 *
 * @author everton.pessoa
 */
class Eventos {

    static function info_evento($id_evento) {
        $arrDadosDb[0] = array('return' => 'Nenhum ID de evento repassado ex. /eventos/{ID_evento}');

        if (!empty($id_evento)) {
            $arrDadosDb = DB::select("SELECT  tc.ds_tarifa, u.ds_nomecompleto as criado_por, m.ds_nomecompleto as modificado_por, c.id_estado, eve.*, DATE_FORMAT(eve.dt_evento, '%d/%m/%Y') AS dt_evento, 
                DATE_FORMAT(eve.dt_criacao, '%d/%m/%Y') AS dt_criacao,
                DATE_FORMAT(eve.dt_modificacao, '%d/%m/%Y') AS dt_modificacao,
                DATE_FORMAT(eve.dt_inicio_inscricao, '%d/%m/%Y') AS dt_inicio_inscricao,
                DATE_FORMAT(eve.dt_fim_inscricao, '%d/%m/%Y') AS dt_fim_inscricao,
                DATE_FORMAT(eve.data_update, '%d/%m/%Y') AS data_update,
                DATE_FORMAT(eve.data_limite_troca_camiseta, '%d/%m/%Y') AS data_limite_troca_camiseta,
                DATE_FORMAT(eve.dt_exibicao_evento, '%d/%m/%Y') AS dt_exibicao_evento
                FROM sa_evento AS eve 
                INNER JOIN sa_evento_diretor AS eved ON eve.id_evento = eved.id_evento 
                LEFT JOIN sa_usuario AS u ON u.id_usuario = eve.id_criador
                LEFT JOIN sa_usuario AS m ON m.id_usuario = eve.id_modificador
                LEFT JOIN sa_cidade AS c ON c.id_cidade = eve.id_cidade AND c.id_pais = eve.id_pais
                LEFT JOIN sa_tarifa_comodidade tc ON tc.id_tarifa_comodidade = eve.id_tarifa_comodidade
                WHERE eved.id_evento = " . $id_evento . " GROUP BY eved.id_evento ORDER BY eve.dt_evento DESC");
        }

        return $arrDadosDb[0];
    }

    static function buscar_por_id($id_evento) {
        $arrDadosDb = array('return' => 'Nenhum ID de evento repassado ex. /eventos/buscar/{ID_evento}');

        if (!empty($id_evento)) {
            $arrDadosDb = Caches::sql("SELECT eve.id_evento, eve.ds_evento, eve.dt_evento, eve.ds_local, eve.vl_porcentagem_adiantamento, eved.id_usuario
                FROM sa_evento AS eve 
                INNER JOIN sa_evento_diretor AS eved ON eve.id_evento = eved.id_evento 
                WHERE eved.id_evento = " . $id_evento . " ORDER BY eve.dt_evento DESC");
        }
        return $arrDadosDb;
    }

    static function editar1() {

        // query do update    
        $returnBanco = DB::table('sa_evento')
                ->where('id_evento', app('request')->input('id_evento'))
                ->update(array('id_situacao_cadastro' => (app('request')->input('id_situacao_cadastro') == 1) ? 1 : 0,
            'fl_inscricao_ativo' => (app('request')->input('fl_inscricao_ativo') == 1) ? 1 : 0,
            'fl_categoria_multipla' => (app('request')->input('fl_categoria_multipla') == 1) ? 1 : 0,
            'fl_cpf_unico' => (app('request')->input('fl_cpf_unico') == 1) ? 1 : 0,
            'hr_evento' => app('request')->input('hr_evento'),
            'dt_evento' => Helpers::formatDataBanco(app('request')->input('dt_evento')),
            'dt_inicio_inscricao' => Helpers::formatDataBanco(app('request')->input('dt_inicio_inscricao')),
            'dt_fim_inscricao' => Helpers::formatDataBanco(app('request')->input('dt_fim_inscricao')),
            'ds_local' => app('request')->input('ds_local'),
            'ds_endereco' => app('request')->input('ds_endereco'),
            'id_pais' => app('request')->input('id_pais'),
            'id_cidade' => app('request')->input('id_cidade'),
            'ds_site' => app('request')->input('ds_site'),
            'ds_descricao_evento' => app('request')->input('ds_descricao_evento'),
            'ds_descricao_evento_resumido' => app('request')->input('ds_descricao_evento_resumido'),
            'ds_retirada_kits' => app('request')->input('ds_retirada_kits'),
            'ds_premiacao' => app('request')->input('ds_premiacao'),
            'ds_regulamento_txt' => app('request')->input('ds_regulamento_txt')
        ));

        if ($returnBanco) {
            // editar as distâncias
            self::editarDistanciaEvento(app('request')->input('id_evento'), app('request')->input('id_distancia_evento'));

            $arrRetorno['status'] = 'true';
            $arrRetorno['msg'] = 'Registro atualizado com sucesso!';
        } else {
            $arrRetorno['status'] = 'false';
            $arrRetorno['msg'] = 'Erro ao tentar efetuar atualização!';
        }

        return ($arrRetorno);
    }

    static function editarDistanciaEvento($idEvento, $arrDistancia) {

        if ($arrDistancia) {
            // query para deletar 
            DB::table('sa_distancia_relacionamento')->where('id_evento', '=', $idEvento)->delete();

            // query para insert
            foreach ($arrDistancia as $idDistancia) {
                DB::table('sa_distancia_relacionamento')->insert(
                        ['id_evento' => $idEvento, 'id_distancia_evento' => $idDistancia]
                );
            }
        }
    }

    static function editar2() {

        // query do update        
        $returnBanco = DB::table('sa_evento')
                ->where('id_evento', app('request')->input('id_evento'))
                ->update(array('fl_encerrar_inscricao' => app('request')->input('fl_encerrar_inscricao'),
            'nr_inscricoes' => app('request')->input('nr_inscricoes'),
            'fl_resultado' => app('request')->input('fl_resultado')
        ));

        if ($returnBanco) {
            $arrRetorno['status'] = 'true';
            $arrRetorno['msg'] = 'Registro atualizado com sucesso!';
        } else {
            $arrRetorno['status'] = 'false';
            $arrRetorno['msg'] = 'Erro ao tentar efetuar atualização!';
        }

        return ($arrRetorno);
    }

    static function cuponsPorEvento($id_usuario) {

        $arrDadosDb = array('return' => 'Nenhum ID do usuário repassado ex. /eventos/cupons/{id_usuario}');

        if (!empty(app('request')->input('id_usuario'))) {
            $id_usuario = app('request')->input('id_usuario');
        }

        if (!empty($id_usuario)) {
            $arrDadosDb = Caches::sql("SELECT
                                            ev.id_evento,
                                            ev.ds_evento,
                                            DATE_FORMAT(ev.dt_evento,'%d/%m/%Y') AS dt_evento,
                                            cid.id_cidade,
                                            cid.ds_cidade,
                                            cd.id_cupom_desconto,
                                            cd.ds_cupom_desconto,
                                            cd.nr_desconto,
                                            DATE_FORMAT(cd.dt_cupom_inicio,'%d/%m/%Y') AS dt_cupom_inicio,
                                            DATE_FORMAT(cd.dt_cupom_termino,'%d/%m/%Y') AS dt_cupom_termino,
                                            cd.en_tipo,
                                            cd.en_tipodesconto,
                                            cd.ds_codigo,
                                            (SELECT COUNT(*) FROM sa_cupom_desconto_item cdi WHERE cdi.id_cupom_desconto = cd.id_cupom_desconto) AS nr_quantidade,
                                            (SELECT COUNT(*) FROM sa_cupom_desconto_item cdi WHERE cdi.id_cupom_desconto = cd.id_cupom_desconto AND cdi.fl_usado = 's') AS nr_usados
                                        FROM
                                            sa_evento ev
                                        INNER JOIN sa_evento_diretor ed ON ed.id_evento = ev.id_evento
                                        INNER JOIN sa_cupom_desconto_evento cde ON cde.id_evento = ev.id_evento
                                        INNER JOIN sa_cupom_desconto cd ON cd.id_cupom_desconto = cde.id_cupom_desconto
                                        INNER JOIN sa_cidade cid ON cid.id_cidade = ev.id_cidade AND cid.id_pais = ev.id_pais
                                        WHERE
                                            ed.id_usuario = " . $id_usuario . "
                                        ORDER BY
                                            ev.dt_evento DESC;");
        }

        return $arrDadosDb;
    }

    static function itensPorCupom($id_cupom) {

        $arrDadosDb = array('return' => 'Nenhum ID de cupom repassado ex. /eventos/cupons/itens/{id_cupom}');

        if (!empty(app('request')->input('id_cupom'))) {
            $id_cupom = app('request')->input('id_cupom');
        }

        if (!empty($id_cupom)) {
            $arrDadosDb = Caches::sql("SELECT * FROm sa_cupom_desconto_item WHERE id_cupom_desconto = " . $id_cupom);
        }

        return $arrDadosDb;
    }

    static function relatorioPorUsuario($id_usuario) {

        $arrDadosDb = array('return' => 'Nenhum ID de usuário repassado ex. /eventos/relatorios/{id_usuario}');

        if (!empty(app('request')->input('id_usuario'))) {
            $id_usuario = app('request')->input('id_usuario');
        }


        if (!empty($id_usuario)) {
            $arrDadosDb = Caches::sql("SELECT ev.id_evento,
                                            ev.ds_evento,
                                            ev.id_situacao_cadastro,
                                            em.nm_modalidade,
                                            em.id_modalidade,
                                            mc.ds_categoria,
                                            mc.id_categoria,
                                            fp.ds_descricao,
                                            fp.id_formas_pagamento,
                                            DATE_FORMAT(ev.dt_evento, '%d/%m/%Y') AS dt_evento,
                                            cid.ds_cidade,
                                            ev.nr_inscricoes,
                                            (
                                                SELECT
                                                    count(pe.id_evento)
                                                FROM
                                                    sa_pedido_evento pe
                                                INNER JOIN sa_pedido p ON p.id_pedido = pe.id_pedido
                                                INNER JOIN sa_pedido_pagamento pp ON pp.id_pedido = p.id_pedido
                                                WHERE
                                                    pe.id_evento = ev.id_evento
                                                AND pe.id_modalidade = em.id_modalidade
                                                AND pe.id_categoria = mc.id_categoria
                                                AND pp.id_formas_pagamento = fp.id_formas_pagamento
                                                GROUP BY pe.id_evento
                                            ) AS incricoes,
                                            (
                                                SELECT
                                                    count(pe.id_evento)
                                                FROM
                                                    sa_pedido_evento pe
                                                INNER JOIN sa_pedido p ON p.id_pedido = pe.id_pedido
                                                INNER JOIN sa_pedido_pagamento pp ON pp.id_pedido = p.id_pedido
                                                WHERE
                                                    pe.id_evento = ev.id_evento
                                                AND pe.id_modalidade = em.id_modalidade
                                                AND pe.id_categoria = mc.id_categoria
                                                AND pp.id_formas_pagamento = fp.id_formas_pagamento
                                                AND p.id_pedido_status = 2
                                                GROUP BY pe.id_evento
                                            ) AS pagos
                                    FROM
                                            sa_evento ev
                                    INNER JOIN sa_evento_diretor ed ON ed.id_evento = ev.id_evento
                                    INNER JOIN sa_cidade cid ON cid.id_cidade = ev.id_cidade
                                    INNER JOIN sa_evento_modalidade em ON em.id_evento = ev.id_evento
                                    INNER JOIN sa_modalidade_categoria mc ON mc.id_modalidade = em.id_modalidade
                                    INNER JOIN sa_formas_pagamento_evento fpe ON fpe.id_evento = ev.id_evento
                                    INNER JOIN sa_formas_pagamento fp ON fp.id_formas_pagamento = fpe.id_formas_pagamento
                                    WHERE  ed.id_usuario = " . $id_usuario . "
                                    GROUP BY
                                        em.id_modalidade,
                                        mc.id_categoria, 
                                        fp.id_formas_pagamento,
                                        ev.id_evento
                                    ORDER BY
                                        ev.id_evento DESC;");
        }

        return $arrDadosDb;
    }

    static function lista_evento_usuario($id_usuario) {

        $arrDadosDb = array('return' => 'Nenhum ID de usuário repassado ex. /eventos/listar/{id_usuario}');

        if (!empty(app('request')->input('id_usuario'))) {
            $id_usuario = app('request')->input('id_usuario');
        }

        if (!empty($id_usuario)) {
            $arrDadosDb = Caches::sql("SELECT ev.id_evento,
                                            ev.ds_evento,
                                            DATE_FORMAT(ev.dt_evento, '%d/%m/%Y') AS dt_evento,
                                            cid.ds_cidade,
                                            (
                                                SELECT count(pe.id_evento)
                                                FROM sa_pedido_evento pe
                                                INNER JOIN sa_pedido p ON p.id_pedido = pe.id_pedido
                                                INNER JOIN sa_pedido_pagamento pp ON pp.id_pedido = p.id_pedido
                                                WHERE
                                                    pe.id_evento = ev.id_evento                                                
                                                AND p.id_pedido_status = 2
                                                GROUP BY pe.id_evento
                                            ) AS incricoes
                                    FROM
                                            sa_evento ev
                                    INNER JOIN sa_evento_diretor ed ON ed.id_evento = ev.id_evento
                                    INNER JOIN sa_cidade cid ON cid.id_cidade = ev.id_cidade                                    
                                    WHERE  ed.id_usuario = " . $id_usuario . "
                                    GROUP BY
                                        ev.id_evento
                                    ORDER BY
                                        ev.id_evento DESC;");
        }

        return $arrDadosDb;
    }

    static function lista_evento_select($id_usuario) {

        $arrDadosDb = array('return' => 'Nenhum ID de usuário repassado ex. /eventos/listar/{id_usuario}');

        if (!empty(app('request')->input('id_usuario'))) {
            $id_usuario = app('request')->input('id_usuario');
        }

        if (!empty($id_usuario)) {
            $arrDadosDb = Caches::sql("SELECT ev.id_evento, ev.vl_porcentagem_adiantamento, ev.ds_evento, DATE_FORMAT(ev.dt_evento, '%d/%m/%Y') AS dt_evento FROM sa_evento ev
                                        INNER JOIN sa_evento_diretor ed ON ed.id_evento = ev.id_evento                             
                                        WHERE  ed.id_usuario = " . $id_usuario . "
                                        GROUP BY ev.id_evento ORDER BY ev.id_evento DESC;");
        }

        return $arrDadosDb;
    }

    static function lista_clientes_evento($id_evento) {

        $arrDadosDb = array('return' => 'Nenhum ID de evento repassado ex. /eventos/detalhes/{id_evento}');

        if (!empty(app('request')->input('id_evento'))) {
            $id_evento = app('request')->input('id_evento');
        }

        if (!empty($id_evento)) {
            $arrDadosDb = Caches::sql("SELECT
                                            p.id_pedido,
                                            pe.id_pedido_evento,
                                            tc.ds_tamanho,
                                            u.ds_nome,
                                            u.nr_documento,
                                            u.nr_celular,
                                            u.ds_email,
                                            u.fl_sexo,
                                            u.pelotao,
                                            (   SELECT
                                                    nm_modalidade
                                                FROM
                                                    sa_evento_modalidade
                                                WHERE
                                                    id_evento = pe.id_evento
                                                    AND pe.id_modalidade = id_modalidade
                                                LIMIT 1
                                            ) AS modalidade,
                                            ( SELECT
                                                    ds_categoria
                                                FROM
                                                    sa_modalidade_categoria
                                                WHERE
                                                    pe.id_categoria = id_categoria
                                                LIMIT 1
                                            ) AS categoria,
                                            cid.ds_cidade,
                                            cid.ds_estado
                                        FROM
                                            sa_pedido_evento pe
                                        INNER JOIN sa_pedido p ON p.id_pedido = pe.id_pedido
                                        INNER JOIN sa_pedido_pagamento pp ON pp.id_pedido = p.id_pedido
                                        INNER JOIN sa_usuario u ON u.id_usuario = p.id_usuario
                                        INNER JOIN sa_cidade cid ON cid.id_cidade = u.id_cidade
                                        INNER JOIN sa_tamanho_camiseta tc ON tc.id_tamanho_camiseta = pe.id_tamanho_camiseta
                                        WHERE
                                            pe.id_evento = " . $id_evento . "
                                        GROUP BY
                                            pe.id_pedido_evento");
        }

        return $arrDadosDb;
    }

    static function modalidades($id_evento) {

        $arrDadosDb = array('return' => 'Nenhum ID de evento repassado ex. /eventos/modalidades/{id_evento}');

        if (!empty(app('request')->input('id_evento'))) {
            $id_evento = app('request')->input('id_evento');
        }

        if (!empty($id_evento)) {
            $arrDadosDb = DB::select("SELECT id_modalidade, nm_modalidade, IF ( id_situacao_cadastro = 0, 'Não', 'Sim') AS status,
                                       id_situacao_cadastro, nr_metros, IF(nr_inscricoes = 0, 'Ilimitado', nr_inscricoes) as qtd_limite, nr_inscricoes,
                                       ds_horario, IF (fl_restrito_idade = 0, 'Não', CONCAT( 'Sim - ', nr_restrito_de, ' até ', nr_restrito_ate ) ) AS fl_restrito_idade, getVendasEventoPorModalidade (id_evento, id_modalidade) AS vendas
                                       FROM sa_evento_modalidade                                       
                                       WHERE id_evento = " . $id_evento);
        }

        return $arrDadosDb;
    }

    static function categorias($id_evento) {

        $arrDadosDb = array('return' => 'Nenhum ID de evento e modalidade repassado ex. /eventos/categorias/{id_evento}');

        if (!empty(app('request')->input('id_evento'))) {
            $id_evento = app('request')->input('id_evento');
        }

        if (!empty($id_evento)) {
            $arrDadosDb = DB::select("SELECT IF(mc.fl_restrito_idade = 0, 'Não', CONCAT( 'Sim - ', mc.nr_restrito_de, ' até ', mc.nr_restrito_ate)) AS restricao,
                                        mc.fl_restrito_idade, mc.id_categoria, mc.nr_restrito_de, mc.nr_restrito_ate, mc.ds_imagem_kit, mc.fl_gratuito, 
                                        mc.fl_camiseta, mc.id_modalidade, mc.ds_categoria, mc.fl_exibir_site, mc.dt_inicio, mc.dt_final,
                                        mc.fl_sexo, mc.fl_permite_inscricao_amigo, mc.ds_kit, mc.nr_quantidade_inscricao, em.ds_modalidade,
                                        getVendasEventoPorCategoria (id_evento, id_categoria) AS vendas,
                                        IF(mc.id_tipo_categoria = 1, 'Individual', 'Revezamento') AS tipo_categoria                                       
                                        FROM sa_modalidade_categoria mc
                                        INNER JOIN sa_evento_modalidade em ON em.id_modalidade = mc.id_modalidade
                                        WHERE em.id_evento = " . $id_evento);
        }

        return $arrDadosDb;
    }

    static function categoriasPorModalidade($id_evento, $id_modalidade) {

        $arrDadosDb = array('return' => 'Nenhum ID de evento e modalidade repassado ex. /eventos/categorias-por-modalidade/{id_evento}/{id_modalidade}');

        if (!empty(app('request')->input('id_evento')) && !empty(app('request')->input('id_modalidade'))) {
            $id_evento = app('request')->input('id_evento');
            $id_modalidade = app('request')->input('id_modalidade');
        }

        if (!empty($id_evento) && !empty($id_modalidade)) {
            $arrDadosDb = DB::select("SELECT IF(mc.fl_restrito_idade = 0, 'Não', CONCAT( 'Sim - ', mc.nr_restrito_de, ' até ', mc.nr_restrito_ate)) AS restricao,
                                        mc.fl_restrito_idade, mc.id_categoria, mc.nr_restrito_de, mc.nr_restrito_ate, mc.ds_imagem_kit, mc.fl_gratuito, 
                                        mc.fl_camiseta, mc.id_modalidade, mc.ds_categoria, mc.fl_exibir_site, mc.dt_inicio, mc.dt_final,
                                        mc.fl_sexo, mc.fl_permite_inscricao_amigo, mc.ds_kit, mc.nr_quantidade_inscricao, em.ds_modalidade,
                                        getVendasEventoPorCategoria (id_evento, id_categoria) AS vendas,
                                        IF(mc.id_tipo_categoria = 1, 'Individual', 'Revezamento') AS tipo_categoria                                       
                                        FROM sa_modalidade_categoria mc
                                        INNER JOIN sa_evento_modalidade em ON em.id_modalidade = mc.id_modalidade
                                        WHERE em.id_evento = " . $id_evento . " AND em.id_modalidade=" . $id_modalidade);
        }

        return $arrDadosDb;
    }

    static function kits($id_evento) {

        $arrDadosDb = array('return' => 'Nenhum ID de evento repassado ex. /eventos/kits/{id_evento}');

        if (!empty(app('request')->input('id_evento'))) {
            $id_evento = app('request')->input('id_evento');
        }

        if (!empty($id_evento)) {
            $arrDadosDb = Caches::sql("SELECT mck.id_modalidade_categoria_kit,
                                            mc.ds_categoria,
                                            em.ds_modalidade,
                                            el.ds_descricao,
                                            format(mck.vl_kit, 2, 'pt_BR') AS vl_kit,
                                            format(mck.vl_kit_assinante, 2, 'pt_BR') AS vl_kit_assinante                                            
                                        FROM sa_modalidade_categoria_kit mck
                                        INNER JOIN sa_modalidade_categoria mc ON mc.id_categoria = mck.id_categoria
                                        INNER JOIN sa_evento_modalidade em ON em.id_modalidade = mc.id_modalidade
                                        INNER JOIN sa_evento_lote el ON el.id_evento_lote = mck.id_evento_lote
                                        WHERE em.id_evento =" . $id_evento);
        }

        return $arrDadosDb;
    }

    static function lotes($id_evento) {

        $arrDadosDb = array('return' => 'Nenhum ID de evento repassado ex. /eventos/lotes/{id_evento}');

        if (!empty(app('request')->input('id_evento'))) {
            $id_evento = app('request')->input('id_evento');
        }

        if (!empty($id_evento)) {
            $arrDadosDb = Caches::sql("SELECT id_evento_lote, DATE_FORMAT(dt_limite, '%d/%m/%Y') AS dt_limite, nr_inscricoes, ds_descricao FROM sa_evento_lote WHERE id_evento = " . $id_evento);
        }

        return $arrDadosDb;
    }

    static function produtos($id_evento) {

        $arrDadosDb = array('return' => 'Nenhum ID de evento repassado ex. /eventos/produtos/{id_evento}');

        if (!empty(app('request')->input('id_evento'))) {
            $id_evento = app('request')->input('id_evento');
        }

        if (!empty($id_evento)) {
            $arrDadosDb = Caches::sql("SELECT
                                            p.id_produto, pc.id_recurso_caracteristica, pr.id_produto_recurso, p.ds_titulo, DATE_FORMAT(p.dt_inicio_exibicao, '%d/%m/%Y') as dt_inicio_exibicao, DATE_FORMAT(p.dt_termino_exibicao, '%d/%m/%Y') as dt_termino_exibicao,
                                             format(p.nr_preco, 2, 'pt_BR') AS nr_preco , pr.nr_quantidade, pc.ds_caracteristica, pc.ds_cor,
                                            (SELECT COUNT(*) as total
                                                FROM
                                                    sa_pedido_produto pp
                                                INNER JOIN sa_pedido p ON p.id_pedido = pp.id_pedido
                                            WHERE pp.id_produto = p.id_produto
                                                AND p.id_pedido_status = 2) as nr_vendas
                                        FROM
                                            sa_produto p
                                        INNER JOIN sa_produto_evento pe ON p.id_produto = pe.id_produto
                                        LEFT JOIN sa_produto_recurso pr ON pr.id_produto = p.id_produto
                                        LEFT JOIN sa_recurso_caracteristica AS pc ON (
                                                pr.id_recurso_1 = pc.id_recurso_caracteristica
                                                OR pr.id_recurso_2 = pc.id_recurso_caracteristica
                                        )
                                        WHERE pe.id_evento = " . $id_evento . " 
                                        GROUP by pc.id_recurso_caracteristica");
        }

        $arrRetorno = array();
        foreach ($arrDadosDb as $objInfo) {
            $arrRetorno[$objInfo->id_produto]['id_produto'] = $objInfo->id_produto;
            $arrRetorno[$objInfo->id_produto]['ds_titulo'] = $objInfo->ds_titulo;
            $arrRetorno[$objInfo->id_produto]['dt_inicio_exibicao'] = $objInfo->dt_inicio_exibicao;
            $arrRetorno[$objInfo->id_produto]['dt_termino_exibicao'] = $objInfo->dt_termino_exibicao;
            $arrRetorno[$objInfo->id_produto]['nr_preco'] = $objInfo->nr_preco;
            $arrRetorno[$objInfo->id_produto]['nr_quantidade'] = $objInfo->nr_quantidade;
            $arrRetorno[$objInfo->id_produto]['nr_vendas'] = $objInfo->nr_vendas;
            $arrRetorno[$objInfo->id_produto]['nr_estoque'] = $objInfo->nr_quantidade - $objInfo->nr_vendas;
            $arrRetorno[$objInfo->id_produto]['ds_caracteristica'][] = $objInfo->ds_caracteristica;
        }

        return $arrRetorno;
    }

    static function questionarios($id_evento) {

        $arrDadosDb = array('return' => 'Nenhum ID de evento repassado ex. /eventos/questionarios/{id_evento}');

        if (!empty(app('request')->input('id_evento'))) {
            $id_evento = app('request')->input('id_evento');
        }

        if (!empty($id_evento)) {
            $arrDadosDb = Caches::sql("SELECT q.id_questionario, q.ds_pergunta, eq.id_categoria, mc.ds_categoria,
                                        IF ( eq.fl_status = 1,  'Ativo', 'Inativo' ) AS fl_status,
                                        IF ( eq.fl_mostra_site = 1, 'Sim', 'Não' ) AS fl_mostra_site,
                                        IF ( eq.fl_mostra_balcao = 1, 'Sim', 'Não' ) AS fl_mostra_balcao
                                        FROM sa_questionario q
                                        INNER JOIN sa_evento_questionario eq ON q.id_questionario = eq.id_questionario
                                        LEFT JOIN sa_modalidade_categoria mc ON mc.id_categoria = eq.id_categoria
                                        WHERE eq.id_evento = " . $id_evento);
        }

        return $arrDadosDb;
    }

    static function camisetas($id_evento) {

        $arrDadosDb = array('return' => 'Nenhum ID de evento repassado ex. /eventos/camisetas/{id_evento}');

        if (!empty(app('request')->input('id_evento'))) {
            $id_evento = app('request')->input('id_evento');
        }

        if (!empty($id_evento)) {
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
                                        WHERE ec.id_evento = " . $id_evento . "
                                        ORDER BY ev.ds_modalidade ASC");
        }

        return $arrDadosDb;
    }

    static function tipos() {
        $arrDadosDb = Caches::sql("SELECT * FROM sa_tipo_evento WHERE id_situacao_cadastro = 1 ORDER BY ds_tipo_evento");

        return $arrDadosDb;
    }

    static function tags($id_evento, $tipo_evento, $select) {

        $arrDadosDb = array('return' => 'Nenhum ID de evento ou Tipo de Evento repassado ex. /eventos/tags/{id_evento}/{tipo_evento}');

        if (!empty($id_evento) && !empty($tipo_evento)) {
            $id_evento = $id_evento;
            $id_tipo_evento = $tipo_evento;
        }

        if (!empty($id_evento) && !empty($id_tipo_evento)) {
            $sqlComplemento = ($select) ? ' AND tr.id_evento = ' . $id_evento : '';

            $arrDadosDb = Caches::sql("SELECT tag.id_tag_evento, tag.ds_tag, tipo.id_tipo_evento, tipo.ds_tipo_evento, tr.id_evento
                                        FROM
                                            sa_tag_evento AS tag
                                        INNER JOIN sa_tipo_evento AS tipo ON tag.id_tipo_evento = tipo.id_tipo_evento
                                        LEFT JOIN sa_tag_relacionamento AS tr ON tag.id_tag_evento = tr.id_tag_evento
                                        WHERE
                                            tipo.id_tipo_evento = " . $tipo_evento . $sqlComplemento . "
                                        GROUP BY
                                            id_tag_evento
                                        ORDER BY
                                            tag.id_tag_evento ASC");
        }

        return $arrDadosDb;
    }

    static function distancias($id_evento, $tipo_evento, $select) {

        $arrDadosDb = array('return' => 'Nenhum ID de evento ou Tipo de Evento repassado ex. /eventos/distancias/{id_evento}/{tipo_evento}');

        if (!empty($id_evento) && !empty($tipo_evento)) {
            $id_evento = $id_evento;
            $id_tipo_evento = $tipo_evento;
        }

        if (!empty($id_evento) && !empty($id_tipo_evento)) {
            $sqlComplemento = ($select) ? ' AND tr.id_evento = ' . $id_evento : '';

            $arrDadosDb = DB::select("SELECT dist.id_distancia_evento, dist.ds_distancia, tipo.id_tipo_evento, tipo.ds_tipo_evento
                                        FROM
                                            sa_distancia_evento AS dist
                                        INNER JOIN sa_tipo_evento AS tipo ON dist.id_tipo_evento = tipo.id_tipo_evento
                                        LEFT JOIN sa_distancia_relacionamento AS tr ON dist.id_distancia_evento = tr.id_distancia_evento
                                        WHERE
                                            tipo.id_tipo_evento = " . $tipo_evento . $sqlComplemento . "
                                        GROUP BY
                                            dist.id_distancia_evento
                                        ORDER BY
                                            dist.id_distancia_evento ASC");
        }

        return $arrDadosDb;
    }

    static function circuitos() {
        $arrDadosDb = Caches::sql("SELECT * FROM sa_circuito ORDER BY circuito ASC");

        return $arrDadosDb;
    }

    static function etapas($id_circuito) {
        $arrDadosDb = Caches::sql("SELECT * FROM sa_etapa WHERE id_circuito = " . $id_circuito);

        return $arrDadosDb;
    }

    static function paises() {
        $arrDadosDb = Caches::sql("SELECT * FROM sa_pais ORDER BY ds_pais ASC");
        $arrSelect['id_pais'] = '';
        $arrSelect['ds_pais'] = ' --- Selecione --- ';
        array_unshift($arrDadosDb, $arrSelect);

        return $arrDadosDb;
    }

    static function estados($id_pais) {
        $arrDadosDb = Caches::sql("SELECT * FROM sa_estado WHERE id_pais = " . $id_pais . " ORDER BY ds_estado ASC");
        $arrSelect['id_estado'] = '';
        $arrSelect['ds_estado'] = ' --- Selecione --- ';
        array_unshift($arrDadosDb, $arrSelect);

        return $arrDadosDb;
    }

    static function cidades($id_pais, $id_estado) {
        $arrDadosDb = Caches::sql("SELECT * FROM sa_cidade WHERE id_pais = " . $id_pais . " AND id_estado = " . $id_estado . " ORDER BY ds_cidade ASC");
        $arrSelect['id_cidade'] = '';
        $arrSelect['ds_cidade'] = ' --- Selecione --- ';
        array_unshift($arrDadosDb, $arrSelect);

        return $arrDadosDb;
    }

    static function imagens($id_evento) {

        $arrDadosDb = array('return' => 'Nenhum ID de evento repassado ex. /eventos/imagens/{id_evento}');

        if (!empty(app('request')->input('id_evento'))) {
            $id_evento = app('request')->input('id_evento');
        }

        if (!empty($id_evento)) {
            $arrDadosDb = Caches::sql("SELECT * FROM sa_evento_imagem_s3 WHERE id_evento = " . $id_evento);
        }

        $arrRetorno = array();
        $arrayTiposImg = array('Logo Evento' => 'img_logo_evento.jpg', 'Banner Evento' => 'img_banner_evento.jpg', 'Resultado Evento' => 'img_resultado_evento.jpg');

        foreach ($arrDadosDb as $objInfo) {

            foreach ($arrayTiposImg as $k => $val) {
                if ($val == $objInfo->ds_imagem) {
                    $arrRetorno[$objInfo->id_evento_imagem]['tipo'] = $k;
                    $arrRetorno[$objInfo->id_evento_imagem]['url'] = 'https://checkout.akamaized.net/' . $objInfo->ds_url_imagem . '/' . $objInfo->ds_imagem;
                }
            }
        }

        return $arrRetorno;
    }

    static function tarifas($id_evento) {

        $arrDadosDb = array('return' => 'Nenhum ID de evento repassado ex. /eventos/tarifas/{id_evento}');

        if (!empty(app('request')->input('id_evento'))) {
            $id_evento = app('request')->input('id_evento');
        }

        if (!empty($id_evento)) {
            $arrDadosDb = Caches::sql("SELECT
                                            tc.ds_tarifa,
                                            tci.vl_inscricao,
                                            tci.fl_tipo_tarifa,
                                            tci.vl_tarifa
                                        FROM sa_tarifa_comodidade tc
                                        INNER JOIN sa_tarifa_comodidade_item tci ON tc.id_tarifa_comodidade = tci.id_tarifa_comodidade
                                        INNER JOIN sa_evento e ON e.id_tarifa_comodidade = tc.id_tarifa_comodidade
                                        WHERE e.id_evento = " . $id_evento);
        }
        $arrRetorno = array();
        foreach ($arrDadosDb as $objInfo) {
            $arrRetorno[] = array('tipo' => ($objInfo->fl_tipo_tarifa) ? 'Percentual' : 'Personalizada', 'tarifa' => 'R$ ' . number_format($objInfo->vl_tarifa, 2, ',', '.'), 'limite' => 'R$ ' . number_format($objInfo->vl_inscricao, 2, ',', '.'));
        }

        return $arrRetorno;
    }

    static function diretores($id_evento) {

        $arrDadosDb = array('return' => 'Nenhum ID de evento repassado ex. /eventos/diretores/{id_evento}');

        if (!empty(app('request')->input('id_evento'))) {
            $id_evento = app('request')->input('id_evento');
        }

        if (!empty($id_evento)) {
            $arrDadosDb = Caches::sql("SELECT u.ds_nomecompleto, ev.id_evento_diretor
                                        FROM sa_usuario u
                                        INNER JOIN sa_evento_diretor ev ON ev.id_usuario = u.id_usuario
                                        WHERE ev.id_evento = " . $id_evento);
        }

        return $arrDadosDb;
    }

    static function precos_fotos() {
        $arrDadosDb = Caches::sql("SELECT id_foto_tabela_preco, ds_foto_tabela_preco
                                        FROM sa_foto_tabela_preco      
                                        WHERE
                                        id_situacao_cadastro = 1");
        return $arrDadosDb;
    }

    static function produtos_assinatura() {
        $arrDadosDb = Caches::sql("SELECT p.id_produto, p.ds_titulo
                                    FROM sa_produto AS p
                                    INNER JOIN sa_produto_categoria_marca AS pc ON p.id_produto = pc.id_produto
                                    WHERE pc.id_contexto_categoria = 19
                                    ORDER BY p.ds_titulo");


        return $arrDadosDb;
    }

    static function listaIdEventosPorUsuario($id_usuario) {
        $arrDadosDb = Caches::sql("SELECT e.id_evento from sa_evento e
                                    INNER JOIN sa_evento_diretor ed ON ed.id_evento = e.id_evento
                                    WHERE ed.id_usuario = " . $id_usuario . " GROUP BY e.id_evento");

        $arrIdEventos = array();

        foreach ($arrDadosDb as $info) {
            $arrIdEventos[] = $info->id_evento;
        }

        $idEvento = implode(',', $arrIdEventos);

        return $idEvento;
    }

}
