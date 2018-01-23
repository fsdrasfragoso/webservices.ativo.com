<?php

namespace App\Http;

use App\Http\Models\Base as Base;

class Helpers {

    static function order_inscritos_all($inscritos) {

        $inscritos_ordened = array();

        foreach ($inscritos as $key => $inscrito) {
            if (isset($inscrito->nm_modalidade)) {
                $key_cat_all = $inscrito->nm_modalidade;
            }

            if (isset($inscrito->ds_categoria)) {
                $key_cat_all = $inscrito->ds_categoria;
            }

            $inscritos_ordened[$key_cat_all][0]['pago'] = $inscrito->pagos;
            $inscritos_ordened[$key_cat_all][0]['pago_total'] = number_format($inscrito->valor_pagos, 2, ',', '.');
            $inscritos_ordened[$key_cat_all][0]['cancelado'] = $inscrito->cancelados;
            $inscritos_ordened[$key_cat_all][0]['cancelado_total'] = number_format($inscrito->valor_cancelados, 2, ',', '.');
            $inscritos_ordened[$key_cat_all][0]['pendente'] = $inscrito->pendentes;
            $inscritos_ordened[$key_cat_all][0]['pendente_total'] = number_format($inscrito->valor_pendentes, 2, ',', '.');
        }

        return $inscritos_ordened;
    }

    static function order_inscritos($inscritos) {

        $inscritos_ordened = array();
        foreach ($inscritos as $key => $inscrito) {

            $key_cat_all = $inscrito->id_evento . '_' . $inscrito->ds_evento;

            $inscritos_ordened[$key_cat_all][0]['pago'] = $inscrito->pagos;
            $inscritos_ordened[$key_cat_all][0]['pago_total'] = number_format($inscrito->valor_pagos, 2, ',', '.');
            $inscritos_ordened[$key_cat_all][0]['cancelado'] = $inscrito->cancelados;
            $inscritos_ordened[$key_cat_all][0]['cancelado_total'] = number_format($inscrito->valor_cancelados, 2, ',', '.');
            $inscritos_ordened[$key_cat_all][0]['pendente'] = $inscrito->pendentes;
            $inscritos_ordened[$key_cat_all][0]['pendente_total'] = number_format($inscrito->valor_pendentes, 2, ',', '.');
        }

        return $inscritos_ordened;
    }

    static function order_inscritos_valores($inscritos_cat) {
        $cates_valores = array();
        foreach ($inscritos_cat as $key => $cate) {
            $cates_valores[$key] = $cate;
            $count = 0;
            foreach ($cates_valores[$key] as $key_c => $ct) {
                if ($count == 0) {
                    $cates_valores[$key . '_total'] = 0;
                }

                $cates_valores[$key . '_total'] += (float) $ct->nr_preco;
                $count++;
            }
            $cates_valores[$key . '_total'] = number_format($cates_valores[$key . '_total'], 2, ',', '.');
        }
        return Helpers::count_valores($cates_valores);
    }

    static function count_valores($objects) {
        foreach ($objects as $key => $object) {
            if (count($object) > 1 || !empty($object[0]->id_atleta)) {
                $objects[$key] = count($object);
            } else {
                $objects[$key] = $object;
            }
        }
        return $objects;
    }

    static function order_pagamentos($pagamentos) {
        $pagamentos_ordened = array();
        foreach ($pagamentos as $key => $pagamento) {
            $forma = $pagamento->forma;

            $pagamentos_ordened[$forma]['cancelado'] = number_format($pagamento->cancelado, 0, '.', '');
            $pagamentos_ordened[$forma]['pendente'] = number_format($pagamento->pendente, 0, '.', '');
            $pagamentos_ordened[$forma]['pago'] = number_format($pagamento->pago, 0, '.', '');
            $pagamentos_ordened[$forma]['total'] = number_format($pagamento->total, 0, '.', '');

            $pagamentos_ordened[$forma]['valor_cancelado'] = number_format($pagamento->valor_cancelado, 2, '.', ',');
            $pagamentos_ordened[$forma]['valor_pendente'] = number_format($pagamento->valor_pendente, 2, '.', ',');
            $pagamentos_ordened[$forma]['valor_pago'] = number_format($pagamento->valor_pago, 2, '.', ',');
            $pagamentos_ordened[$forma]['valor_total'] = number_format($pagamento->valor_total, 2, '.', ',');
        }

        return $pagamentos_ordened;
    }

    static function count_pagamentos($objects) {
        foreach ($objects as $key => $object) {
            foreach ($object as $key_o => $obj) {
                if (count($obj) > 1 || !empty($obj[0]->id_pedido)) {
                    $objects[$key][$key_o] = count($obj);
                } else {
                    $objects[$key][$key_o] = $obj;
                }
            }
        }
        return $objects;
    }

    static function formatDataBanco($strData) {
        return date('Y-m-d', strtotime(str_replace('/', '-', $strData)));
    }

    static function formatValorCalc($intValor) {
        return str_replace(',', '.', str_replace('.', '', $intValor));
    }

}
