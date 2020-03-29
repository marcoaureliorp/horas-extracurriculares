<?php

namespace App\API\Controller;

use App\Model\DB\Documento;
use Exception;

class Documentos {

    /**
     * @param $dados
     * @return bool|int
     * @throws Exception
     */
    public function salvar($dados) {
        $documento = new Documento();

        return isset($dados[Documento::COL_DOCUMENTO_ID])
            ? $documento->alterar($dados)
            : $documento->adicionar($dados);
    }

    /**
     * @param $dados
     * @return array
     */
    public function listar($dados) {
        $documento = new Documento();

        $usuario_id = $dados[Documento::COL_USUARIO_ID] ?? null;
        $ordem = $dados['ordem'] ?? null;
        $limite = $dados['ordem'] ?? null;

        return $documento->listar($usuario_id, $ordem, $limite);
    }

    /**
     * @param $dados
     * @return array|mixed
     */
    public function selecionar($dados) {
        $documento = new Documento();
        return $documento->selecionar($dados['documento_id']);
    }

    /**
     * @param $dados
     * @return bool
     */
    public function excluir($dados) {
        $documento = new Documento();
        return $documento->excluir($dados[Documento::COL_DOCUMENTO_ID]);
    }

}