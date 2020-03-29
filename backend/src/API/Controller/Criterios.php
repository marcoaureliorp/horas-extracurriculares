<?php

namespace App\API\Controller;

use App\Model\DB\Criterio;
use App\Model\DB\Curso;
use Exception;

class Criterios {

    /**
     * @param $dados
     * @return bool|int
     * @throws Exception
     */
    public function salvar($dados) {
        $criterio = new Criterio();

        return isset($dados[Criterio::COL_CRITERIO_ID])
            ? $criterio->alterar($dados)
            : $criterio->adicionar($dados);
    }

    /**
     * @param $dados
     * @return array
     */
    public function listar($dados) {
        $criterio = new Criterio();

        $criterio_id = $dados[Criterio::COL_CRITERIO_ID] ?? null;
        $curso_id = $dados[Curso::COL_CURSO_ID] ?? null;
        $ordem = $dados['ordem'] ?? null;
        $limite = $dados['ordem'] ?? null;

        return $criterio->listar($criterio_id, $curso_id, $ordem, $limite);
    }

    /**
     * @param $dados
     * @return bool
     */
    public function excluir($dados) {
        $criterio = new Criterio();
        return $criterio->excluir($dados[Criterio::COL_CRITERIO_ID]);
    }

}