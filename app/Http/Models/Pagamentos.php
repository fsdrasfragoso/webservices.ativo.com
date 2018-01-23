<?php

namespace App\Http\Models;

use App\Http\Helpers as Helpers;
use App\Http\Caches as Caches;

class Pagamentos {

    static function getPreSql($where = '') {

        $sql_inscritos = "SELECT	
                            fp.ds_descricao AS forma,
                            SUM(IF (ped.id_pedido_status = 5, 1, 0)) AS cancelado,
                            SUM(IF (ped.id_pedido_status = 5, pev.nr_preco, 0)) AS valor_cancelado,
                            SUM(IF (ped.id_pedido_status = 1, 1, 0)) AS pendente,
                            SUM(IF (ped.id_pedido_status = 1, pev.nr_preco, 0)) AS valor_pendente,
                            SUM(IF (ped.id_pedido_status = 2, 1, 0)) AS pago,
                            SUM(IF (ped.id_pedido_status = 2, pev.nr_preco, 0)) AS valor_pago,
                            SUM(1) AS total,
                            SUM(pev.nr_preco) as valor_total
                        FROM
                            sa_pedido_evento AS pev
                        INNER JOIN sa_pedido AS ped ON ped.id_pedido = pev.id_pedido
                        INNER JOIN sa_evento AS e ON pev.id_evento = e.id_evento
                        INNER JOIN sa_evento_diretor AS u ON pev.id_evento = u.id_evento
                        INNER JOIN sa_pedido_pagamento pg ON pg.id_pedido = ped.id_pedido
                        INNER JOIN sa_formas_pagamento fp ON fp.id_formas_pagamento = pg.id_formas_pagamento
                        WHERE
                         " . $where . "
                        GROUP BY
                            e.id_evento, fp.ds_descricao
                        ORDER BY
                            total DESC";

        return $sql_inscritos;
    }

    static function get_tipos_pagamento($id_evento) {

        $pagamentos_evento = array('return' => 'Nenhum ID de evento repassado ex. /pagamentos/tipos/{ID_evento}');

        $idUsuario = app('request')->input('user_id');

        if (!empty($id_evento)) {
            $srtWhere = "e.id_evento = " . $id_evento . " AND u.id_usuario = " . $idUsuario;
        } else {
            $srtWhere = "u.id_usuario = " . $idUsuario;
        }

        $pagamentos_evento = Caches::sql(self::getPreSql($srtWhere));

        if (!empty($pagamentos_evento)) {
            $pagamentos_evento = Helpers::order_pagamentos($pagamentos_evento);
        }

        return $pagamentos_evento;
    }

}
