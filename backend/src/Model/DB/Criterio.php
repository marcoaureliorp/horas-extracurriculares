<?php

namespace App\Model\DB;

use Blazar\Component\Dao\CRUDMysql;
use Blazar\Core\Log;
use Blazar\Core\Manifest;
use Exception;
use GUMP;

class Criterio extends CRUDMysql {

    const TABELA = "criterio";

    const COL_CRITERIO_ID = "criterio_id";
    const COL_CURSO_ID = "curso_id";
    const COL_MODALIDADE_ID = "modalidade_id";
    const COL_DESCRICAO = "descricao";
    const COL_HORAS = "horas";
    const COL_UNIDADE = "unidade";
    const COL_DESATIVADO = "desativado";

    /**
     * Modalidade constructor.
     */
    public function __construct() {
        parent::__construct(Manifest::db("main_db"));
    }

    /**
     * @param $dados
     * @return bool|int
     * @throws Exception
     */
    public function adicionar($dados) {

        if (isset($dados[self::COL_CRITERIO_ID])) {
            throw new Exception("Index " . self::COL_CRITERIO_ID . " não é permitido no método adicionar.");
        }

        $dados = $this->cleanStruct($dados);

        $is_valid = GUMP::is_valid($dados, [
            self::COL_DESCRICAO => 'required|max_len,255'
        ]);

        // Verifica se existe erros
        if ($is_valid !== true) {
            throw new Exception(implode("\r\n", $is_valid));
        }

        $retorno = 0;

        try {
            $retorno = $this->create(self::TABELA, $dados);
        } catch (Exception $e) {
            Log::e("Model Critério", $e->getMessage() . "\n" . $e->getTraceAsString(), false, self::TABELA);
        }

        return $retorno;
    }

    /**
     * @param $dados
     * @return bool|int
     * @throws Exception
     */
    public function alterar($dados) {
        $dados = $this->cleanStruct($dados);

        $is_valid = GUMP::is_valid($dados, [
            self::COL_DESCRICAO => 'required|max_len,255'
        ]);

        // Verifica se existe erros
        if ($is_valid !== true) {
            throw new Exception(implode("\r\n", $is_valid));
        }

        $where_con = self::COL_CRITERIO_ID . " = ?";
        $where_val = [$dados[self::COL_CRITERIO_ID]];

        try {
            $retorno = $this->update(self::TABELA, $dados, $where_con, $where_val);
        } catch (Exception $e) {
            Log::e("Model Critério", $e->getMessage() . "\n" . $e->getTraceAsString(), false, self::TABELA);
            return false;
        }

        return $dados[self::COL_CRITERIO_ID];
    }

    /**
     * @param null $criterio_id
     * @param null $curso_id
     * @param null $ordem
     * @param null $limite
     * @return array
     */
    public function listar($criterio_id = null, $curso_id = null, $ordem = null, $limite = null) {
        $where_con = self::COL_DESATIVADO . " = ?";
        $where_val = [0];

        if (!empty($criterio_id)) {
            $where_con .= " AND " . self::COL_CRITERIO_ID . " = ?";
            $where_val[] = $criterio_id;
        }

        if (!empty($curso_id)) {
            $where_con .= " AND " . self::COL_CURSO_ID . " = ?";
            $where_val[] = $curso_id;
        }

        $ordem = !empty($ordem) ? $ordem : self::COL_DESCRICAO . " ASC";

        try {
            return $this->read(self::TABELA, "*", $where_con, $where_val, null, null, $ordem, $limite);
        } catch (Exception $e) {
            Log::e("Model Critério", $e->getMessage() . "\n" . $e->getTraceAsString(), false, self::TABELA);
            return [];
        }
    }

    /**
     * @param $criterio_id
     * @return bool
     */
    public function excluir($criterio_id) {
        $where_con = self::COL_CRITERIO_ID . " = ?";
        $where_val = [$criterio_id];

        $dados = [self::COL_DESATIVADO => 1];

        try {
            return $this->update(self::TABELA, $dados, $where_con, $where_val);
        } catch (Exception $e) {
            Log::e("Model Critério", $e->getMessage() . "\n" . $e->getTraceAsString(), false, self::TABELA);
            return false;
        }
    }
}