<?php

namespace App\Model\DB;

use Blazar\Component\Dao\CRUDMysql;
use Blazar\Core\Manifest;

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

    public function listar() {
        try {
            $result = $this->read(self::TABELA, "*", null, [], null, null, null, null);
            return $result;
        } catch (\Exception $e) {
            return [];
        }
    }
}