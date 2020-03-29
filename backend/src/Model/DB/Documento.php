<?php

namespace App\Model\DB;

use Blazar\Component\Dao\CRUDMysql;
use Blazar\Core\Log;
use Blazar\Core\Manifest;
use Exception;

class Documento extends CRUDMysql {

    const TABELA = "documento";

    const COL_DOCUMENTO_ID = "documento_id";
    const COL_CRITERIO_ID = "criterio_id";
    const COL_USUARIO_ID = "usuario_id";
    const COL_DATA = "data";
    const COL_APROVACAO = "aprovacao";

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

        if (isset($dados[self::COL_DOCUMENTO_ID])) {
            throw new Exception("Index " . self::COL_DOCUMENTO_ID . " não é permitido no método adicionar.");
        }

        $anexos = $dados['anexos'] ?? [];
        unset($dados['anexos']);

        $dados = $this->cleanStruct($dados);

        $retorno = 0;

        try {
            $retorno = $this->create(self::TABELA, $dados);

            if (!empty($anexos)) {
                $anexo = new Anexo();

                foreach ((array)$anexos as $files) {
                    $files = is_array($files) ? $files : json_decode($files, true);

                    foreach ($files as $file) {
                        $anexo->salvar($file, $dados[self::COL_DOCUMENTO_ID]);
                    }
                }
            }
        } catch (Exception $e) {
            Log::e("Model Documento", $e->getMessage() . "\n" . $e->getTraceAsString(), false, self::TABELA);
        }

        return $retorno;
    }

    /**
     * @param $dados
     * @return bool|int
     * @throws Exception
     */
    public function alterar($dados) {
        $anexos = $dados['anexos'] ?? [];
        unset($dados['anexos']);

        $dados = $this->cleanStruct($dados);

        $where_con = self::COL_DOCUMENTO_ID . " = ?";
        $where_val = [$dados[self::COL_DOCUMENTO_ID]];

        try {
            $this->update(self::TABELA, $dados, $where_con, $where_val);

            if (!empty($anexos)) {
                $anexo = new Anexo();

                foreach ((array)$anexos as $files) {
                    $files = is_array($files) ? $files : json_decode($files, true);

                    foreach ($files as $file) {
                        $anexo->salvar($file, $dados[self::COL_DOCUMENTO_ID]);
                    }
                }
            }
        } catch (Exception $e) {
            Log::e("Model Documento", $e->getMessage() . "\n" . $e->getTraceAsString(), false, self::TABELA);
            return false;
        }

        return $dados[self::COL_DOCUMENTO_ID];
    }

    /**
     * @param null $usuario_id
     * @param null $ordem
     * @param null $limite
     * @return array
     */
    public function listar($usuario_id = null, $ordem = null, $limite = null) {
        $where_con = "1 = 1";
        $where_val = [];

        if (!empty($usuario_id)) {
            $where_con .= " AND " . self::COL_USUARIO_ID . " = ?";
            $where_val[] = $usuario_id;
        }

        $ordem = !empty($ordem) ? $ordem : self::COL_DATA . " DESC";

        try {
            return $this->read(self::TABELA, "*", $where_con, $where_val, null, null, $ordem, $limite);
        } catch (Exception $e) {
            Log::e("Model Documento", $e->getMessage() . "\n" . $e->getTraceAsString(), false, self::TABELA);
            return [];
        }
    }

    /**
     * @param $documento_id
     * @return array|mixed
     */
    public function selecionar($documento_id) {
        $where_con = self::COL_DOCUMENTO_ID . " = ?";
        $where_val = [$documento_id];

        try {
            $retorno = $this->read(self::TABELA, "*", $where_con, $where_val);
            $retorno = isset($retorno[0]) ? $retorno[0] : $retorno;

            if (!empty($retorno)) {
                $anexo = new Anexo();
                $retorno['anexos'] = $anexo->listar($documento_id);
            }

            return $retorno;
        } catch (Exception $e) {
            Log::e("Model Documento", $e->getMessage() . "\n" . $e->getTraceAsString(), false, self::TABELA);
            return [];
        }
    }

    /**
     * @param $documento_id
     * @return bool
     */
    public function excluir($documento_id) {
        $where_con = self::COL_DOCUMENTO_ID . " = ?";
        $where_val = [$documento_id];

        try {
            return $this->delete(self::TABELA, $where_con, $where_val);
        } catch (Exception $e) {
            Log::e("Model Documento", $e->getMessage() . "\n" . $e->getTraceAsString(), false, self::TABELA);
            return false;
        }
    }
}