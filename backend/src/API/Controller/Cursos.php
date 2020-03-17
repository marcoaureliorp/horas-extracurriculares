<?php

namespace App\API\Controller;

use App\Model\DB\Curso;
use Exception;

class Cursos {

    /**
     * @param $dados
     * @return bool|int
     * @throws Exception
     */
    public function salvar($dados) {
        $curso = new Curso();

        return isset($dados[Curso::COL_CURSO_ID])
            ? $curso->alterar($dados)
            : $curso->adicionar($dados);
    }

    /**
     * @param $dados
     * @return array
     */
    public function listar($dados) {
        $curso = new Curso();

        $curso_id = $dados[Curso::COL_CURSO_ID] ?? null;
        $ordem = $dados['ordem'] ?? null;
        $limite = $dados['ordem'] ?? null;

        return $curso->listar($curso_id, $ordem, $limite);
    }

    /**
     * @param $dados
     * @return bool
     */
    public function excluir($dados) {
        $curso = new Curso();
        return $curso->excluir($dados[Curso::COL_CURSO_ID]);
    }

}