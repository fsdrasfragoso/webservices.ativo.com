<?php

namespace App\Http\Models;

use App\Http\Caches as Caches;
use App\Http\Models\Pagamentos as Pagamentos;

class Base {

    static function get_evento_id($id_evento) {

        $eventos = array('return' => 'Nenhum ID de evento repassado ex. /eventos_id/{ID_evento}');

        if (!empty($id_evento)) {

            $eventos = Caches::sql("SELECT eve.id_evento, eve.ds_evento, eve.dt_evento, eved.id_usuario
                FROM sa_evento AS eve 
                INNER JOIN sa_evento_diretor AS eved ON eve.id_evento = eved.id_evento 
                WHERE eved.id_evento=" . $id_evento . " ORDER BY eve.dt_evento DESC");
        } else {

            $idUsuario = app('request')->input('user_id');

            $eventos = Caches::sql("SELECT eve.id_evento, eve.ds_evento, eve.dt_evento, eved.id_usuario
                FROM sa_evento AS eve 
                INNER JOIN sa_evento_diretor AS eved ON eve.id_evento = eved.id_evento 
                WHERE eved.id_usuario = " . $idUsuario . " ORDER BY eve.dt_evento DESC");
        }

        return $eventos;
    }

    static function get_eventos($id_usuario) {

        $eventos_usuario = array('return' => 'Nenhum ID de usuario repassado ex. /eventos/{ID_usuario}');

        if (!empty(app('request')->input('user_id'))) {
            $id_usuario = app('request')->input('user_id');
        }

        if (!empty($id_usuario)) {
            $eventos_usuario = Caches::sql("SELECT eve.id_evento, ci_eve.ds_cidade, eve.ds_evento,
                eve.dt_evento AS date_order,
                DATE_FORMAT(eve.dt_evento,'%d/%m/%Y') AS dt_evento
                FROM sa_evento AS eve 
                INNER JOIN sa_cidade AS ci_eve ON ci_eve.id_cidade=eve.id_cidade AND ci_eve.id_pais=eve.id_pais
                INNER JOIN sa_evento_diretor AS eved ON eve.id_evento=eved.id_evento 
                WHERE eved.id_usuario=" . $id_usuario . " GROUP BY eve.id_evento ORDER BY date_order");
        }
      
        foreach ($eventos_usuario as $key => $evento) {
            $inscritos = Pagamentos::get_tipos_pagamento($evento->id_evento);

            $evento->ds_inscritos = $inscritos;
            $evento->ds_inscritos_total = 0;
            $evento->ds_valor_total = 0;
            foreach ($inscritos as $ins) {
                $evento->ds_inscritos_total += $ins['total'];
            }
        }

        return $eventos_usuario;
    }

    static function get_modalidades($id_evento) {
        $modalidades_evento = array('return' => 'Nenhum ID de evento repassado ex. /modalidades/{ID_evento}');

        if (!empty($id_evento)) {
            $modalidades_evento = Caches::sql("SELECT id_modalidade, nm_modalidade 
                                                FROM sa_evento_modalidade 
                                                WHERE id_evento=" . $id_evento . " ORDER BY nm_modalidade ASC");
        } else {

            $idUsuario = app('request')->input('user_id');
            $modalidades_evento = Caches::sql("SELECT id_modalidade, nm_modalidade 
                FROM sa_evento_modalidade  AS md
                INNER JOIN sa_evento_diretor AS u ON md.id_evento=u.id_evento
                WHERE u.id_usuario = " . $idUsuario . " ORDER BY nm_modalidade ASC");
        }

        return $modalidades_evento;
    }

    static function get_categorias($id_evento) {

        $categorias_evento = array('return' => 'Nenhum ID de evento repassado ex. /categorias/{ID_evento}');

        if (!empty($id_evento)) {
            $categorias_evento = Caches::sql("SELECT cat.id_modalidade, cat.id_categoria, cat.ds_categoria, moda.nm_modalidade 
                                            FROM sa_modalidade_categoria AS cat
                                            INNER JOIN sa_evento_modalidade AS moda ON cat.id_modalidade=moda.id_modalidade 
                                            WHERE cat.id_modalidade IN (SELECT id_modalidade FROM sa_evento_modalidade WHERE id_evento = " . $id_evento . ") ORDER BY ds_categoria ASC");
        } else {
            $idUsuario = app('request')->input('user_id');
            $categorias_evento = Caches::sql("SELECT cat.id_modalidade, cat.id_categoria, cat.ds_categoria, moda.nm_modalidade 
                                            FROM sa_modalidade_categoria AS cat
                                            INNER JOIN sa_evento_modalidade AS moda ON cat.id_modalidade=moda.id_modalidade
                                            WHERE cat.id_modalidade IN (
                                                SELECT id_modalidade 
                                                FROM sa_evento_modalidade as emo 
                                                INNER JOIN sa_evento_diretor AS u ON emo.id_evento=u.id_evento 
                                                WHERE u.id_usuario = " . $idUsuario . ") 
                                            ORDER BY ds_categoria ASC");
        }

        return $categorias_evento;
    }

}
