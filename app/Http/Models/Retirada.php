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

    static function sincronizarRetiradaEvento() {

        $arrDados = app('request')->input('dados');
        $idEvento = app('request')->input('id_evento');
        if ($arrDados) {
            foreach ($arrDados as $name => $value) {
                $salvar[] = '(' . '"' . $value['cod_retirado'] . '", "' . $value['cod_retirado_info'] . '", "' . $value['id_evento'] . '", "' . $value['id_inscritos'] . '", "' . $value['id_inscritos_produto'] . '", "' . $value['retirado'] . '"' . ')';
            }

            DB::insert('INSERT INTO sa_pedido_retirado(id_pedido_retirado, id_pedido_retirado_info, id_evento, id_pedido_evento, id_pedido_produto, retirado) VALUES ' . implode(',', $salvar) . ' ON DUPLICATE KEY UPDATE id_pedido_retirado = VALUES(id_pedido_retirado)');

            $arrRetorno['status'] = 'ok';
            $arrRetorno['dados'] = 'Sincronização efetuada - Retiradas - Evento ' . $idEvento;
        } else {
            $arrRetorno['status'] = 'error';
            $arrRetorno['dados'] = 'Nenhuma informação para ser sincronizada';
        }


        return $arrRetorno;
    }

    static function sincronizarRetiradaInfoEvento() {

        $arrDados = app('request')->input('dados');
        $idEvento = app('request')->input('id_evento');

        if ($arrDados) {
            foreach ($arrDados as $name => $value) {
                $ehComprador = (isset($value['eh_comprador']) && $value['eh_comprador'] > 0) ? 1 : 0;
                $nome = (isset($value['nome'])) ? $value['nome'] : '';
                $telefone = (isset($value['telefone'])) ? $value['telefone'] : '';
                $obs = (isset($value['obs'])) ? $value['obs'] : '';

                $salvar[] = '("' . $value['cod_retirado_info'] . '", "1", "' . $value['id_pedido'] . '", "' . $ehComprador . '", "' . $nome . '", "' . $telefone . '", "' . $obs . '", "' . $value['dt_alterado'] . '")';
            }

            DB::insert('INSERT INTO sa_pedido_retirado_info (id_pedido_retirado_info, id_pedido_retirado_funcionario, id_pedido, comprador_retirou, nome, telefone, obs, dt_retirado) 
                    VALUES ' . implode(',', $salvar) . ' ON DUPLICATE KEY UPDATE id_pedido_retirado_info = VALUES(id_pedido_retirado_info)');

            $arrRetorno['status'] = 'ok';
            $arrRetorno['dados'] = 'Sincronização efetuada - Retiradas Info - Evento ' . $idEvento;
        } else {
            $arrRetorno['status'] = 'error';
            $arrRetorno['dados'] = 'Nenhuma informação para ser sincronizada';
        }

        return $arrRetorno;
    }

    static function sincronizarInscricoesEvento() {

        $arrDados = app('request')->input('dados');
        $idEvento = app('request')->input('id_evento');

        if ($arrDados) {
            foreach ($arrDados as $name => $value) {
                DB::update('UPDATE sa_pedido_evento SET id_modalidade = ' . $value['id_modalidade'] . ', id_categoria = ' . $value['id_categoria'] . ', nr_peito = ' . $value['nm_peito'] . ' WHERE id_pedido_evento = ' . $value['cod_inscritos']);
            }

            $arrRetorno['status'] = 'ok';
            $arrRetorno['dados'] = 'Sincronização efetuada - Inscrições - Evento ' . $idEvento;
        } else {
            $arrRetorno['status'] = 'error';
            $arrRetorno['dados'] = 'Nenhuma informação para ser sincronizada';
        }

        return $arrRetorno;
    }

    static function sincronizarNovasInscricoesEvento() {

        $arrRetorno['status'] = 'ok';
        $arrRetorno['dados'] = 'Sincronização efetuada - Novas Inscrições - Evento ' . $idEvento;

        return $arrRetorno;
        // ajustes


        $arrDados = app('request')->input('dados');
        $idEvento = app('request')->input('id_evento');

        if ($arrDados) {
            foreach ($arrDados as $name => $value) {

                $idCodigoNovaInscricao = "RETI-" . $value['cod_inscritos_novo'];

                // verificando se já existe um pedido pagamento para essa nova inscrição
                $boolPedidoPagamento = self::buscarPedidoPagamento($idCodigoNovaInscricao);

                // Verificar não existe esta inscrição
                if (!$boolPedidoPagamento) {

                    // Procurar Usuario Pelo Email
                    $objUsuario = self::buscarUsuarioPorEmail($value['email']);

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
                                    'nm_necessidades_especiais' => $value['necessidades'],
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
                                'nr_peito' => isset($value['nm_peito']) ? $value['nm_peito'] : null,
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

            $arrRetorno['status'] = 'ok';
            $arrRetorno['dados'] = 'Sincronização efetuada - Novas Inscrições - Evento ' . $idEvento;
        } else {
            $arrRetorno['status'] = 'error';
            $arrRetorno['dados'] = 'Nenhuma informação para ser sincronizada';
        }

        return $arrRetorno;
    }

    static function sincronizarUsuariosEvento() {

        $arrDados = app('request')->input('dados');
        $idEvento = app('request')->input('id_evento');


        if ($arrDados) {
            foreach ($arrDados as $name => $value) {

                if ($value['id_pedido_evento']) {
                    // busco o pedido evento
                    $objPedidoEvento = self::buscarPedidoEvento($value['id_pedido_evento']);

                    // verificando qual tipo de usuário é o pedido
                    if (isset($objPedidoEvento) && $objPedidoEvento->id_usuario_balcao > 0) {

                        // busco o usuário com o e-mail informado e o id usuário do evento
                        $objUsuarioBalcao = self::buscarUsuarioBalcaoPorEmail($value['email'], $objPedidoEvento->id_usuario);

                        if (!$objUsuarioBalcao) {
                            $idUsuarioBalcao = self::salvarUsuarioBalcao(array(
                                        'nome' => $value['nome'],
                                        'email' => $value['email'],
                                        'tipo_documento' => 1,
                                        'documento' => $value['documento'],
                                        'nascimento' => $value['dtnascimento'],
                                        'genero' => $value['genero'],
                                        'telefone' => $value['telefone'],
                                        'celular' => $value['celular'],
                                        'cadastro' => $value['dt_alterado'],
                                        'id_usuario_adm' => $objPedidoEvento->id_usuario
                            ));
                        } else {
                            $idUsuarioBalcao = $objUsuarioBalcao->id_usuario;
                        }

                        // atualizando pedidoEvento
                        self::updateUsuarioPedidoEvento($idUsuarioBalcao, $objPedidoEvento->id_pedido_evento);
                    } else {
                        // busco o usuário com o e-mail informado e o id usuário do evento
                        $objUsuario = self::buscarUsuarioPorEmail($value['email']);
                        if (!$objUsuario) {
                            $idUsuario = self::salvarUsuario(array(
                                        'cod_funcionario' => 1,
                                        'nome_completo' => $value['nome'],
                                        'email' => $value['email'],
                                        'h_tipo_cpf' => 1,
                                        'v_nr_documento' => $value['documento'],
                                        'nascimento' => $value['dtnascimento'],
                                        'genero' => $value['genero'],
                                        'id_cidade' => '',
                                        'telefone' => $value['telefone'],
                                        'celular' => $value['celular'],
                                        'nm_necessidades_especiais' => $value['necessidades'],
                                        'equipe' => $value['equipe'],
                                        'dt_alterado' => $value['dt_alterado']
                            ));
                        } else {
                            $idUsuario = $objUsuario->id_usuario;
                        }

                        // atualizando pedidoEvento
                        self::updateUsuarioPedidoEvento($idUsuario, $objPedidoEvento->id_pedido_evento);

                        // carrego o objeto pedido
                        $objPedido = self::buscarPedido($objPedidoEvento->id_pedido);

                        // verifico se o dono do pedido é diferente do usuário atual para adicionar como amigo
                        if ($objPedido->id_usuario != $idUsuario) {
                            // verificando se é usuario amigo
                            $objUsuarioAmigo = self::buscarUsuarioAmigo($objPedido->id_usuario, $idUsuario);

                            // salvando o resgistro de usuário amigo
                            if (!$objUsuarioAmigo) {
                                self::salvarUsuarioAmigo(array(
                                    'id_usuario' => $objPedido->id_usuario,
                                    'id_usuario_amigo' => $idUsuario
                                ));
                            }
                        }
                    }

                    // update de usuários que não tem inscrição
                } else {
                    // se for usuário balcão
                    if ($value['fl_balcao'] == 1) {
                        self::updateUsuarioTable('sa_usuario_balcao', array(
                            'ds_nome' => $value['nome'],
                            'ds_celular' => $value['celular'],
                            'ds_telefone' => $value['telefone'],
                            'dt_nascimento' => $value['dtnascimento']
                                )
                                , $value['cod_usuario']);
                    } else {
                        $pos_espaco = strpos($value['nome'], ' ');
                        $arrDados = array(
                            'ds_nome' => trim(substr($value['nome'], 0, $pos_espaco)),
                            'ds_sobrenome' => trim(substr($value['nome'], $pos_espaco + 1, strlen($value['nome']))),
                            'ds_celular' => $value['celular'],
                            'ds_telefone' => $value['telefone'],
                            'dt_nascimento' => $value['dtnascimento']);
                        
                        self::updateUsuarioTable('sa_usuario', $arrDados, $value['cod_usuario']);
                    }
                }
            }
            $arrRetorno['status'] = 'ok';
            $arrRetorno['dados'] = 'Sincronização efetuada - Usuários - Evento ' . $idEvento;
        } else {
            $arrRetorno['status'] = 'error';
            $arrRetorno['dados'] = 'Nenhuma informação para ser sincronizada';
        }

        return $arrRetorno;
    }

    static function sincronizarFuncionariosEvento() {
        $arrDados = app('request')->input('dados');
        $idEvento = app('request')->input('id_evento');

        if ($arrDados) {
            foreach ($arrDados as $name => $value) {
                $salvar[] = '(' . $value['cod_funcionario'] . ', "' . $value['nome'] . '"' . ')';
            }

            DB::insert('INSERT IGNORE INTO sa_pedido_retirado_funcionario (id_pedido_retirado_funcionario, nome) VALUES ' . implode(',', $salvar));

            $arrRetorno['status'] = 'ok';
            $arrRetorno['dados'] = 'Sincronização efetuada - Funcionários - Evento ' . $idEvento;
        } else {
            $arrRetorno['status'] = 'error';
            $arrRetorno['dados'] = 'Nenhuma informação para ser sincronizada';
        }

        return $arrRetorno;
    }

    /* metodos de suporte para a api */

    static function salvarUsuario($arrDados) {

        $pos_espaco = strpos($arrDados['nome_completo'], ' ');

        $arrDadosUsuario = array('id_tipo_usuario' => 5,
            'ds_nome_contato' => $arrDados['cod_funcionario'],
            'ds_nome' => trim(substr($arrDados['nome_completo'], 0, $pos_espaco)),
            'ds_sobrenome' => trim(substr($arrDados['nome_completo'], $pos_espaco + 1, strlen($arrDados['nome_completo']))),
            'ds_email' => $arrDados['email'],
            'id_tipo_documento' => $arrDados['h_tipo_cpf'],
            'nr_documento' => str_replace(array('-', '.'), '', $arrDados['v_nr_documento']),
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

    static function salvarUsuarioBalcao($arrDados) {

        $arrDadosUsuario = array(
            'ds_nome' => $arrDados['nome'],
            'ds_email' => $arrDados['email'],
            'id_tipo_documento' => $arrDados['tipo_documento'],
            'nr_documento' => str_replace(array('-', '.'), '', $arrDados['documento']),
            'dt_nascimento' => $arrDados['nascimento'],
            'fl_sexo' => $arrDados['genero'],
            'ds_telefone' => $arrDados['telefone'],
            'ds_celular' => $arrDados['celular'],
            'dt_cadastro' => $arrDados['cadastro'],
            'id_usuario_adm' => $arrDados['id_usuario_adm']
        );

        return DB::table('sa_usuario_balcao')->insertGetId($arrDadosUsuario);
    }

    static function salvarUsuarioAmigo($arrDados) {
        $arrDadosUsuarioAmigo = array(
            'id_usuario' => $arrDados['id_usuario'],
            'id_usuario_amigo' => $arrDados['id_usuario_amigo']
        );

        return DB::table('sa_usuario_amigo')->insertGetId($arrDadosUsuarioAmigo);
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
            'nr_peito' => $arrDados['nr_peito'],
            'fl_amigo' => 1,
            'nm_qtd' => 1,
            'nr_preco' => $arrDados['nr_preco'],
            'dt_cadastro' => $arrDados['dt_cadastro']
        );

        return DB::table('sa_pedido_evento')->insertGetId($arrDadosPedidoEvento);
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
            'nr_valor' => $arrDados['nr_preco'],
            'nr_valor_pago' => $arrDados['nr_preco'],
            'dt_registro' => $arrDados['dt_alterado'],
            'dt_pagamento' => $arrDados['dt_alterado'],
            'txt_resultado' => $arrDados['cod_inscricao'],
            'fl_status' => 'CONFIRMADO',
            'nr_parcelas' => 1
        );

        return DB::table('sa_pedido_pagamento')->insertGetId($arrDadosPedidoPagamento);
    }

    static function buscarUsuarioPorEmail($strEmail) {
        return DB::table('sa_usuario')->where('ds_email', $strEmail)->first();
        // return DB::table('sa_usuario')->WhereRaw('MATCH ds_email AGAINST (\'"' . $strEmail . '"\')')->first();
    }

    static function buscarUsuarioBalcaoPorEmail($strEmail, $idUsuario) {
        return DB::table('sa_usuario_balcao')->where('ds_email', $strEmail)->where('id_usuario_adm', $idUsuario)->first();
    }

    static function buscarUsuarioAmigo($idUsuario, $idAmigo) {
        return DB::table('sa_usuario_amigo')->where('id_usuario', $idAmigo)->where('id_usuario_amigo', $idUsuario)->first();
    }

    static function buscarPedido($idPedido) {
        return DB::table('sa_pedido')->where('id_pedido', $idPedido)->first();
    }

    static function buscarPedidoEvento($idPedidoEvento) {
        return DB::table('sa_pedido_evento')->where('id_pedido_evento', $idPedidoEvento)->first();
    }

    static function buscarPedidoPagamento($info) {
        return DB::table('sa_pedido_pagamento')->WhereRaw('MATCH txt_resultado AGAINST (\'"' . $info . '"\')')->first();
    }

    static function updateUsuarioPedidoEvento($idUsuario, $idPedidoEvento) {
        return DB::update('UPDATE sa_pedido_evento SET id_usuario = "' . $idUsuario . '" WHERE id_pedido_evento = ' . $idPedidoEvento);
    }

    static function updateUsuarioTable($table, $arrDados, $idUsuario) {
        return DB::table($table)->where('id_usuario', $idUsuario)->update($arrDados);
    }

}
