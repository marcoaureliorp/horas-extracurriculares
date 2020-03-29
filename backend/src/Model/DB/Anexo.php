<?php

namespace App\Model\DB;

use Blazar\Component\Dao\CRUDMysql;
use Blazar\Core\Log;
use Blazar\Core\Manifest;
use Exception;

class Anexo extends CRUDMysql {

    const TABELA = "anexo";
    const TABELA_RELACAO = "anexo_documento";

    const COL_ANEXO_ID = "anexo_id";
    const COL_NOME = "nome";
    const COL_TITULO = "titulo";
    const COL_DATA = "data";

    /**
     * Modalidade constructor.
     */
    public function __construct() {
        parent::__construct(Manifest::db("main_db"));
    }

    /**
     * @param $anexos
     * @param $documento_id
     * @return bool|int
     * @throws Exception
     */
    public function salvar($anexos, $documento_id) {
        return isset($anexos[self::COL_ANEXO_ID])
            ? $this->alterar($anexos, $documento_id)
            : $this->adicionar($anexos, $documento_id);
    }

    /**
     * @param $anexo_id
     * @param $documento_id
     * @return bool
     */
    public function salvarRelacao($anexo_id, $documento_id) {
        if (!empty($this->verificaRelacao($anexo_id, $documento_id))) {
            $this->removeRelacao($anexo_id, $documento_id);
        }

        $dados = [
            self::COL_ANEXO_ID => $anexo_id,
            Documento::COL_DOCUMENTO_ID => $documento_id
        ];

        try {
            $this->create(self::TABELA_RELACAO, $dados);
            return true;
        } catch (Exception $e) {
            Log::e("Model Anexo", $e->getMessage() . "\n" . $e->getTraceAsString(), false, self::TABELA);
            return false;
        }
    }

    /**
     * @param $anexo_id
     * @param $documento_id
     * @return array
     */
    public function verificaRelacao($anexo_id, $documento_id) {
        $where_con = self::COL_ANEXO_ID . " = ? AND " . Documento::COL_DOCUMENTO_ID . " = ?";
        $where_val = [$anexo_id, $documento_id];

        try {
            return $this->read(self::TABELA_RELACAO, "*", $where_con, $where_val);
        } catch (Exception $e) {
            Log::e("Model Anexo", $e->getMessage() . "\n" . $e->getTraceAsString(), false, self::TABELA);
            return [];
        }
    }

    /**
     * @param $anexo_id
     * @param $documento_id
     * @return bool
     */
    public function removeRelacao($anexo_id, $documento_id) {
        $where_con = self::COL_ANEXO_ID . " = ? AND " . Documento::COL_DOCUMENTO_ID . " = ?";
        $where_val = [$anexo_id, $documento_id];

        try {
            return $this->delete(self::TABELA_RELACAO, $where_con, $where_val);
        } catch (Exception $e) {
            Log::e("Model Anexo", $e->getMessage() . "\n" . $e->getTraceAsString(), false, self::TABELA);
            return false;
        }
    }

    /**
     * @param $dados
     * @param $documento_id
     * @return bool|int
     * @throws Exception
     */
    public function adicionar($dados, $documento_id) {

        if (isset($dados[self::COL_ANEXO_ID])) {
            throw new Exception("Index " . self::COL_ANEXO_ID . " não é permitido no método adicionar.");
        }

        $dados = $this->cleanStruct($dados);

        $retorno = 0;

        try {
            $retorno = $this->create(self::TABELA, $dados);

            $this->salvarRelacao($retorno, $documento_id);
        } catch (Exception $e) {
            Log::e("Model Anexo", $e->getMessage() . "\n" . $e->getTraceAsString(), false, self::TABELA);
        }

        return $retorno;
    }

    /**
     * @param $dados
     * @param $documento_id
     * @return bool|int
     */
    public function alterar($dados, $documento_id) {
        $dados = $this->cleanStruct($dados);

        $retorno = 0;

        $where_con = self::COL_ANEXO_ID . " = ?";
        $where_val = [$dados[self::COL_ANEXO_ID]];

        try {
            $retorno = $this->update(self::TABELA, $dados, $where_con, $where_val);

            $this->salvarRelacao($dados[self::COL_ANEXO_ID], $documento_id);
        } catch (Exception $e) {
            Log::e("Model Anexo", $e->getMessage() . "\n" . $e->getTraceAsString(), false, self::TABELA);
        }

        return $retorno;
    }

    /**
     * @param null $documento_id
     * @param null $ordem
     * @param null $limite
     * @return array
     */
    public function listar($documento_id = null, $ordem = null, $limite = null) {
        $table = self::TABELA . " a " .
            " LEFT JOIN " . self::TABELA_RELACAO . " ad ON a." . self::COL_ANEXO_ID . " = ad." . self::COL_ANEXO_ID;

        $campos = "a.*";

        $where_con = "ad." . Documento::COL_DOCUMENTO_ID . " = ?";
        $where_val = [$documento_id];

        $ordem = !empty($ordem) ? $ordem : self::COL_DATA . " DESC, " . self::COL_TITULO . " ASC";

        try {
            return $this->read($table, $campos, $where_con, $where_val, null, null, $ordem, $limite);
        } catch (Exception $e) {
            Log::e("Model Anexo", $e->getMessage() . "\n" . $e->getTraceAsString(), false, self::TABELA);
            return [];
        }
    }

    /**
     * @param $anexo_id
     * @param $documento_id
     * @return bool
     */
    public function excluir($anexo_id, $documento_id) {
        $where_con = self::COL_ANEXO_ID . " = ?";
        $where_val = [$anexo_id];

        try {
            $this->removeRelacao($anexo_id, $documento_id);

            return $this->delete(self::TABELA, $where_con, $where_val);
        } catch (Exception $e) {
            Log::e("Model Anexo", $e->getMessage() . "\n" . $e->getTraceAsString(), false, self::TABELA);
            return false;
        }
    }
}