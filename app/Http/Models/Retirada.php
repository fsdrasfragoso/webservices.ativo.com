<?php

namespace App\Http\Models;

use Illuminate\Support\Facades\DB;
use App\Http\Caches as Caches;
use App\Http\Models\Usuario as Usuario;

class Retirada {

    static function proximosEventos() {

        $arrRetorno['status'] = 'error';
        $arrRetorno['dados'] = 'Nenhum retorno para /retirada/proximos-eventos';

        $arrDadosDb = Caches::sql("CALL proc_webservice_retirada_proximos_eventos()");

        if ($arrDadosDb) {
            $arrRetorno['status'] = 'ok';
            $arrRetorno['dados'] = $arrDadosDb;
        }

        return $arrRetorno;
    }

    static function modalidadesEventos($intIdEvento) {

        $arrRetorno['status'] = 'error';
        $arrRetorno['dados'] = 'Nenhum retorno para /retirada/categorias-eventos';

        $arrDadosDb = Caches::sql("CALL proc_webservice_retirada_modalidades_evento(" . $intIdEvento . ")");

        if ($arrDadosDb) {
            $arrRetorno['status'] = 'ok';
            $arrRetorno['dados'] = $arrDadosDb;
        }

        return $arrRetorno;
    }

    static function categoriasEventos($intIdEvento) {
        $arrRetorno['status'] = 'error';
        $arrRetorno['dados'] = 'Nenhum retorno para /retirada/categorias-eventos';

        $arrDadosDb = Caches::sql("CALL proc_webservice_retirada_categorias_evento(" . $intIdEvento . ")");

        if ($arrDadosDb) {
            $arrRetorno['status'] = 'ok';
            $arrRetorno['dados'] = $arrDadosDb;
        }

        return $arrRetorno;
    }

    static function carregarEvento($intIdEvento, $tipo) {

        $arrRetorno['status'] = 'error';
        $arrRetorno['dados'] = 'Nenhum retorno para /retirada/carregar-evento/' . $intIdEvento;

        if (!$intIdEvento) {
            $arrRetorno['status'] = 'error';
            $arrRetorno['dados'] = 'Nenhum ID de usuario repassado ex. /retirada/carregar-evento/{ID_EVENTO}';
        }

        $arrDadosDb = Caches::sql("SELECT id_evento, ds_evento, dt_evento, hr_evento, fl_encerrar_inscricao FROM sa_evento WHERE id_evento = " . $intIdEvento);

        if ($arrDadosDb) {

            $arrDadosDb[0]->modalidades = self::modalidadesEventos($intIdEvento);
            $arrDadosDb[0]->categorias = self::categoriasEventos($intIdEvento);
            $arrDadosDb[0]->camisetas = self::camisetasEvento($intIdEvento);

            // $arrDadosDb[0]->inscritos = self::inscritosEvento($intIdEvento, $tipo);
            // $arrDadosDb[0]->usuarios = self::usuariosEvento($intIdEvento, $tipo);
            $arrDadosDb[0]->produtos = self::pedidosProdutosEvento($intIdEvento);

            $arrRetorno['status'] = 'ok';
            $arrRetorno['dados'] = $arrDadosDb[0];
        }

        return $arrRetorno;
    }

    static function inscritosEvento($intIdEvento, $tipo) {

        $arrRetorno['status'] = 'error';
        $arrRetorno['dados'] = 'Nenhum retorno para /retirada/inscritos-evento/' . $intIdEvento . '/' . $tipo;

        if (!$intIdEvento) {
            $arrRetorno['status'] = 'error';
            $arrRetorno['dados'] = 'Nenhum ID de evento repassado ex. /retirada/inscritos-evento/{ID_EVENTO}/{TIPO}';
        }

        $arrDadosDb = Caches::sql("CALL proc_webservice_inscritos_evento_tipo_retirada(" . $intIdEvento . ", '" . $tipo . "')");

        if ($arrDadosDb) {
            $arrRetorno['status'] = 'ok';
            $arrRetorno['dados'] = $arrDadosDb;
        }

        return $arrRetorno;
    }

    static function usuariosEvento($intIdEvento, $intTipo) {
        $arrRetorno['status'] = 'error';
        $arrRetorno['dados'] = 'Nenhum retorno para /retirada/usuarios-evento/' . $intIdEvento;

        if (!$intIdEvento) {
            $arrRetorno['status'] = 'error';
            $arrRetorno['dados'] = 'Nenhum ID de evento repassado ex. /retirada/usuarios-evento/{ID_EVENTO}';
        }

        $arrDadosDb = Caches::sql("CALL proc_webservice_usuarios_evento(" . $intIdEvento . ", '" . $intTipo . "')");

        if ($arrDadosDb) {
            $arrRetorno['status'] = 'ok';
            $arrRetorno['dados'] = $arrDadosDb;
        }

        return $arrRetorno;
    }

    static function pedidosProdutosEvento($intIdEvento) {
        $arrRetorno['status'] = 'error';
        $arrRetorno['dados'] = 'Nenhum retorno para /retirada/pedidos-produtos-evento/' . $intIdEvento;

        if (!$intIdEvento) {
            $arrRetorno['status'] = 'error';
            $arrRetorno['dados'] = 'Nenhum ID de evento repassado ex. /retirada/pedidos-produtos-evento/{ID_EVENTO}';
        }

        $arrDadosDb = Caches::sql("CALL proc_webservice_produtos_evento(" . $intIdEvento . ")");

        if ($arrDadosDb) {
            $arrRetorno['status'] = 'ok';
            $arrRetorno['dados'] = $arrDadosDb;
        }

        return $arrRetorno;
    }

    static function camisetasEvento($intIdEvento) {
        $arrRetorno['status'] = 'error';
        $arrRetorno['dados'] = 'Nenhum retorno para /retirada/camisetas-evento/' . $intIdEvento;

        if (!$intIdEvento) {
            $arrRetorno['status'] = 'error';
            $arrRetorno['dados'] = 'Nenhum ID de evento repassado ex. /retirada/camisetas-evento/{ID_EVENTO}';
        }

        $arrDadosDb = Caches::sql("CALL proc_webservice_retirada_camisetas_por_evento(" . $intIdEvento . ")");

        if ($arrDadosDb) {
            $arrRetorno['status'] = 'ok';
            $arrRetorno['dados'] = $arrDadosDb;
        }

        return $arrRetorno;
    }

    /* Fluxo de retirada */

    static function sincronizarRetiradaEvento($intIdEvento) {

        $arrDados = app('request')->input('dados');

        foreach ($arrDados as $name => $value) {
            $salvar[] = '(' . '"' . $value['cod_retirado'] . '", "' . $value['cod_retirado_info'] . '", "' . $value['id_evento'] . '", "' . $value['id_inscritos'] . '", "' . $value['id_inscritos_produto'] . '", "' . $value['retirado'] . '"' . ')';

            echo 'INSERT INTO sa_pedido_retirado (  id_pedido_retirado, id_pedido_retirado_info, id_evento, id_pedido_evento, id_pedido_produto, retirado) 
            VALUES ' . implode(',', $salvar) . ' ON DUPLICATE KEY UPDATE id_pedido_retirado = VALUES(id_pedido_retirado)';

            var_dump($salvar);
            die();

            DB::query('INSERT INTO sa_pedido_retirado(id_pedido_retirado, id_pedido_retirado_info, id_evento, id_pedido_evento, id_pedido_produto, retirado) VALUES ' . implode(',', $salvar) . ' ON DUPLICATE KEY UPDATE id_pedido_retirado = VALUES(id_pedido_retirado)');
        }

        return 'sincronizar retiradas - ' . $intIdEvento;
    }

    static function sincronizarRetiradaInfoEvento($intIdEvento) {

        $arrDados = app('request')->input('dados');

        foreach ($arrDados as $name => $value) {
            $salvar[] = '("' . $value['cod_retirado_info'] . '", "' . $value['cod_funcionario'] . '", "' . $value['id_pedido'] . '", "' . intval($value['eh_comprador'] > 0) ? 1 : 0 . '", "' . $value['nome'] . '", "' . $value['telefone'] . '", "' . $value['obs'] . '", "' . $value['dt_alterado'] . '")';
        }

        echo 'INSERT INTO sa_pedido_retirado_info (id_pedido_retirado_info, id_pedido_retirado_funcionario, id_pedido, comprador_retirou, nome, telefone, obs, dt_retirado) 
                    VALUES ' . implode(',', $salvar) . ' ON DUPLICATE KEY UPDATE id_pedido_retirado_info = VALUES(id_pedido_retirado_info)';
        var_dump($salvar);
        die();

        DB::query('INSERT INTO sa_pedido_retirado_info (id_pedido_retirado_info, id_pedido_retirado_funcionario, id_pedido, comprador_retirou, nome, telefone, obs, dt_retirado) 
                    VALUES ' . implode(',', $salvar) . ' ON DUPLICATE KEY UPDATE id_pedido_retirado_info = VALUES(id_pedido_retirado_info)');

        return 'sincronizar retiradas info - ' . $intIdEvento;
    }

    static function sincronizarInscricoesEvento($intIdEvento) {

        $arrDados = app('request')->input('dados');

        foreach ($arrDados as $name => $value) {
            DB::query('UPDATE sa_pedido_evento SET id_modalidade = ' . $value['id_modalidade'] . ', id_categoria = ' . $value['id_categoria'] . ', nr_peito = ' . $value['nm_peito'] . ' WHERE id_pedido_evento = ' . $value['cod_inscritos']);
        }
        return 'sincronizar inscrições - ' . $intIdEvento;
    }

    static function sincronizarNovasInscricoesEvento($intIdEvento) {

        $arrDados = app('request')->input('dados');

        foreach ($arrDados as $name => $value) {

            var_dump($value);
            die();
            $idCodigoNovo = "RETI-" . $value['cod_inscritos_novo'];

            // verificando se já existe um pedido pagamento para essa nova inscrição
            $boolPedidoPagamento = self::buscarPedidoPagamento($idCodigoNovaInscricao);
            var_dump($boolPedidoPagamento);
            die();
            // Verificar se já existe esta inscrição
            if (!$boolPedidoPagamento) {

                // Procurar Usuario Pelo Email
                $objUsuario = self::buscarPorEmail($value['email']);

                var_dump($objUsuario);
                die();

                // verificando se o usuário existe no banco, se não existir é criado um novo usuário
                if (!$objUsuario) {
                    $idUsuario = self::salvarUsuario(array(
                                'cod_funcionario' => $value['cod_funcionario'],
                                'nome_completo' => $value['nome_completo'],
                                'email' => $value['email'],
                                'h_tipo_cpf' => $value['h_tipo_cpf'],
                                'v_nr_documento' => $value['v_nr_documento'],
                                'nascimento' => date('Y-m-d', strtotime($value['nascimento3'] . '-' . $value['nascimento2'] . '-' . $value['nascimento1'])),
                                'genero' => $value['genero'],
                                'id_cidade' => $value['id_cidade'],
                                'telefone' => $value['telefone'],
                                'celular' => $value['celular'],
                                'nm_necessidades_especiais' => $value['nm_necessidades_especiais'],
                                'equipe' => $value['equipe'],
                                'dt_alterado' => $value['dt_alterado']
                    ));
                } else {
                    $idUsuario = $objUsuario->id_usuario;
                }

                // salvando o pedido
                $idPedido = self::salvarPedido(array(
                            'id_usuario' => $idUsuario,
                            'nm_preco' => $value['nm_preco'],
                            'dt_alterado' => $value['dt_alterado']
                ));

                // salvando o pedido evento
                $idPedidoEvento = self::salvarPedidoEvento(array(
                            'id_pedido' => $idPedido,
                            'id_evento' => $value['id_evento'],
                            'id_modalidade' => $value['modalidade'],
                            'id_categoria' => $value['categoria'],
                            'id_usuario' => $idUsuario,
                            'id_camiseta' => $value['camiseta'],
                            'nr_peito' => $value['nm_peito'],
                            'nr_preco' => $value['nm_preco'],
                            'dt_cadastro' => $value['dt_alterado']
                ));

                // salvando o pedido pagamento
                $idPedidoPagamento = self::salvarPedidoPagamento(array(
                            'id_pedido' => $idPedido,
                            'id_usuario' => $idUsuario,
                            'forma_pagamento' => $value['camiseta'],
                            'nr_preco' => $value['nm_preco'],
                            'dt_alterado' => $value['dt_alterado'],
                            'cod_inscricao' => $idCodigoNovaInscricao
                ));
            }
        }


        return 'sincronizar novas inscrições - ' . $intIdEvento;
    }

    static function sincronizarUsuariosEvento($intIdEvento) {
        return 'sincronizar usuários - ' . $intIdEvento;
    }

    static function sincronizarFuncionariosEvento($intIdEvento) {

        foreach ($funcionario as $name => $value) {
            $salvar[] = '(' . $value['cod_funcionario'] . ', "' . $value['nome'] . '"' . ')';
        }

        echo 'INSERT IGNORE INTO sa_pedido_retirado_funcionario (id_pedido_retirado_funcionario, nome) VALUES ' . implode(',', $salvar);
        var_dump($salvar);
        die();

        DB::query('INSERT IGNORE INTO sa_pedido_retirado_funcionario (id_pedido_retirado_funcionario, nome) VALUES ' . implode(',', $salvar));

        return 'sincronizar funcionários - ' . $intIdEvento;
    }

    /* metodos de suporte para a api */

    static function salvarUsuario($arrDados) {

        $arrDadosUsuario = array('id_tipo_usuario' => 5,
            'ds_nome_contato' => $arrDados['cod_funcionario'],
            'ds_nome' => $arrDados['nome_completo'],
            'ds_email' => $arrDados['email'],
            'id_tipo_documento' => $arrDados['h_tipo_cpf'],
            'nr_documento' => $arrDados['v_nr_documento'],
            'dt_nascimento' => $arrDados['nascimento'],
            'fl_sexo' => $arrDados['genero'],
            'id_cidade' => $arrDados['id_cidade'],
            'nr_telefone' => $arrDados['telefone'],
            'nr_celular' => $arrDados['celular'],
            'nm_necessidades_especiais' => $arrDados['nm_necessidades_especiais'],
            'ds_equipe' => $arrDados['equipe'],
            'data_create' => $arrDados['dt_alterado'],
            'data_update' => $arrDados['dt_alterado'],
            'ds_senha' => md5(0),
        );

        return DB::table('sa_usuario')->insertGetId($arrDadosUsuario);
    }

    static function salvarPedido($arrDados) {
        $arrDadosPedido = array(
            'id_usuario' => $arrDados['id_usuario'],
            'id_pedido_status' => 2,
            'nr_total' => $arrDados['nm_preco'],
            'dt_pedido' => $arrDados['dt_alterado'],
            'fl_evento_da_casa' => 1,
            'fl_local_inscricao' => 3
        );

        return DB::table('sa_pedido')->insertGetId($arrDadosPedido);
    }

    static function salvarPedidoEvento($arrDados) {

        $arrDadosPedidoEvento = array(
            'id_pedido' => $arrDados['id_pedido'],
            'id_evento' => $arrDados['id_evento'],
            'id_modalidade' => $arrDados['id_modalidade'],
            'id_categoria' => $arrDados['id_categoria'],
            'id_usuario' => $arrDados['id_usuario'],
            'id_tamanho_camiseta' => $arrDados['id_camiseta'],
            'nr_peito' => $arrDados['nm_peito'],
            'fl_amigo' => 1,
            'nm_qtd' => 1,
            'nr_preco' => $arrDados['nm_preco'],
            'dt_cadastro' => $arrDados['dt_alterado']
        );

        return DB::table('sa_pedido_evento')->insertGetId($arrDadosPedido);
    }

    static function salvarPedidoPagamento($arrDados) {

        switch ($arrDados['forma_pagamento']) {
            case 1:
                $idFormaPagamento = 18;
                break;
            case 2:
                $idFormaPagamento = 19;
                break;
            case 3:
                $idFormaPagamento = 20;
                break;
            default :
                $idFormaPagamento = 18;
        }


        $arrDadosPedidoPagamento = array('id_pedido' => $arrDados['id_pedido'],
            'id_usuario' => $arrDados['id_usuario'],
            'id_formas_pagamento' => $idFormaPagamento,
            'nr_valor' => $arrDados['nm_preco'],
            'nr_valor_pago' => $arrDados['nm_preco'],
            'dt_registro' => $arrDados['dt_alterado'],
            'dt_pagamento' => $arrDados['dt_alterado'],
            'txt_resultado' => $arrDados['cod_inscricao'],
            'fl_status' => 'CONFIRMADO',
            'nr_parcelas' => 1
        );

        return DB::table('sa_pedido_pagamento')->insertGetId($arrDadosPedidoPagamento);
    }

    static function buscarPorEmail($strEmail) {
        return DB::table('sa_usuario')->where('ds_email', $strEmail)->first();
    }

    static function buscarPedidoPagamento($info) {
        return DB::table('sa_pedido_pagamento')->where('txt_resultado', $info)->first();
    }

}
