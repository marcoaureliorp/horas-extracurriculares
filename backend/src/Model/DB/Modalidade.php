<?php

namespace App\Model\DB;

use Blazar\Component\Dao\CRUDMysql;
use Blazar\Core\Log;
use Blazar\Core\Manifest;
use Exception;
use GUMP;

class Modalidade extends CRUDMysql {

    const TABELA = "modalidade";

    const COL_MODALIDADE_ID = "modalidade_id";
    const COL_NOME = "nome";

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

        if (isset($dados[self::COL_MODALIDADE_ID])) {
            throw new Exception("Index " . self::COL_MODALIDADE_ID . " não é permitido no método adicionar.");
        }

        $dados = $this->cleanStruct($dados);

        $is_valid = GUMP::is_valid($dados, [
            self::COL_NOME => 'required|max_len,255'
        ]);

        // Verifica se existe erros
        if ($is_valid !== true) {
            throw new Exception(implode("\r\n", $is_valid));
        }

        $retorno = 0;

        try {
            $retorno = $this->create(self::TABELA, $dados);
        } catch (Exception $e) {
            Log::e("Model Modalidade", $e->getMessage() . "\n" . $e->getTraceAsString(), false, self::TABELA);
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
            self::COL_NOME => 'required|max_len,255'
        ]);

        // Verifica se existe erros
        if ($is_valid !== true) {
            throw new Exception(implode("\r\n", $is_valid));
        }

        $retorno = 0;

        $where_con = self::COL_MODALIDADE_ID . " = ?";
        $where_val = [$dados[self::COL_MODALIDADE_ID]];

        try {
            $retorno = $this->update(self::TABELA, $dados, $where_con, $where_val);
        } catch (Exception $e) {
            Log::e("Model Modalidade", $e->getMessage() . "\n" . $e->getTraceAsString(), false, self::TABELA);
        }

        return $retorno;
    }

    /**
     * @param null $modalidade_id
     * @param null $ordem
     * @param null $limite
     * @return array
     */
    public function listar($modalidade_id = null, $ordem = null, $limite = null) {
        $where_con = "1 = 1";
        $where_val = [];

        if (!empty($modalidade_id)) {
            $where_con .= " AND " . self::COL_MODALIDADE_ID . " = ?";
            $where_val[] = $modalidade_id;
        }

        $ordem = !empty($ordem) ? $ordem : self::COL_NOME . " ASC";

        try {
            return $this->read(self::TABELA, "*", $where_con, $where_val, null, null, $ordem, $limite);
        } catch (Exception $e) {
            Log::e("Model Modalidade", $e->getMessage() . "\n" . $e->getTraceAsString(), false, self::TABELA);
            return [];
        }
    }

    /**
     * @param $modalidade_id
     * @return bool
     */
    public function excluir($modalidade_id) {
        $where_con = self::COL_MODALIDADE_ID . " = ?";
        $where_val = [$modalidade_id];

        try {
            return $this->delete(self::TABELA, $where_con, $where_val);
        } catch (Exception $e) {
            Log::e("Model Modalidade", $e->getMessage() . "\n" . $e->getTraceAsString(), false, self::TABELA);
            return false;
        }
    }
}