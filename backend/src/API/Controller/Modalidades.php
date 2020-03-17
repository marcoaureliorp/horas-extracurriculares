<?php

namespace App\API\Controller;

use App\Model\DB\Modalidade;
use Exception;

class Modalidades {

    /**
     * @param $dados
     * @return bool|int
     * @throws Exception
     */
    public function salvar($dados) {
        $modalidade = new Modalidade();

        return isset($dados['modalidade_id'])
            ? $modalidade->alterar($dados)
            : $modalidade->adicionar($dados);
    }

    /**
     * @param $dados
     * @return array
     */
    public function listar($dados) {
        $modalidade = new Modalidade();

        $modalidade_id = $dados[Modalidade::COL_MODALIDADE_ID] ?? null;
        $ordem = $dados['ordem'] ?? null;
        $limite = $dados['ordem'] ?? null;

        return $modalidade->listar($modalidade_id, $ordem, $limite);
    }

    /**
     * @param $dados
     * @return bool
     */
    public function excluir($dados) {
        $modalidade = new Modalidade();
        return $modalidade->excluir($dados['modalidade_id']);
    }

}