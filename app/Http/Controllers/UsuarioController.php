<?php

namespace App\Http\Controllers;

use App\Http\Models\Usuario as Usuario;
use App\Http\Caches as Caches;

class UsuarioController {

    function login() {
        $arrDados = Usuario::login();
        return response()->json($arrDados);
    }

    function minhaConta($intIdUsuario) {
        $arrDados = Usuario::minhaConta($intIdUsuario);
        return response()->json($arrDados);
    }

    function novoCadastro() {
        $arrDados = Usuario::novoCadastro();
        return response()->json($arrDados);
    }

    function editarCadastro($intIdUsuario) {
        $arrDados = Usuario::editarCadastro($intIdUsuario);
        return response()->json($arrDados);
    }

    function amigos($intIdUsuario) {
        $arrDados = Usuario::amigos($intIdUsuario);
        return response()->json($arrDados);
    }

    function addAmigos($intIdUsuario) {
        $arrDados = Usuario::addAmigos($intIdUsuario);
        return response()->json($arrDados);
    }

    function remAmigos($intIdUsuario) {
        $arrDados = Usuario::remAmigos($intIdUsuario);
        return response()->json($arrDados);
    }

    function resultados($intIdUsuario) {
        $arrDados = Usuario::resultados($intIdUsuario);
        return response()->json($arrDados);
    }

    function inscricoes($intIdUsuario) {
        $arrDados = Usuario::inscricoes($intIdUsuario);
        return response()->json($arrDados);
    }

    function fotos($intIdUsuario) {
        $arrDados = Usuario::fotos($intIdUsuario);
        return response()->json($arrDados);
    }

    function buscarPorEmail($strEmail) {
        $arrDados = Usuario::buscarPorEmail($strEmail);
        return response()->json($arrDados);
    }

}
