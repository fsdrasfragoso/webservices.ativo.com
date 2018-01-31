<?php

namespace App\Http;

class Helpers {

    static function formatDataBanco($strData) {
        return date('Y-m-d', strtotime(str_replace('/', '-', $strData)));
    }

    static function formatValorCalc($intValor) {
        return str_replace(',', '.', str_replace('.', '', $intValor));
    }

    static function gerarPdfCertificado($arrDados) {

        $arrFiltros = array('evento' => '%NProva%', 'local' => '%NCidade%', 'nome' => '%NAME%', 'tempo_final' => '%TFinal%', 'distancia' => '%Per%',
            'pace' => '%PMedio%', 'tempo_bruto' => '%TBruto%', 'peito' => '%NPeito%', 'classificacao' => '%CTotal%');

        $htmlCertificado = file_get_contents($arrDados->template);

        foreach ($arrFiltros as $key => $filtro) {
            $htmlCertificado = str_replace($filtro, $arrDados->$key, $htmlCertificado);
        }

        return $htmlCertificado;
    }

}
