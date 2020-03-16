<?php

namespace App\Helpers;

use Blazar\Core\Log;
use Exception;
use GUMP;

/**
 * Class GumpAddValidator
 * @package Nucleo\Helpers\GUMP
 */
final class GUMPAddValidator {
    private static $aplicados = array();

    /**
     * Adiciona um validador e verifica se
     * ele já não foi adicionado
     *
     * @param $validacao
     * @param $call
     * @throws Exception
     */
    public static function add($validacao, $call) {
        if (in_array($validacao, self::$aplicados)) {
            return;
        }

        GUMP::add_validator($validacao, $call);

        self::$aplicados[] = $validacao;
    }

    /**
     * Adiciona todos validadores pre-definidos
     */
    public static function addPreDefinidos() {
        try {
            self::checkDate();
        } catch (Exception $e) {
            Log::e($e);
        }
    }

    /**
     * Validador de data pre-definido
     *
     * @throws Exception
     */
    private static function checkDate() {
        if (in_array("check_date", self::$aplicados)) {
            return;
        }

        GUMP::add_validator("check_date", function ($field, $input, $param = null) {
            $date_format = 'Y-m-d H:i:s';
            $time = strtotime($input[$field]);

            if (date($date_format, $time) != $input[$field]) {
                return false;
            }

            return true;
        }, "Formato da data é inválido (Y-m-d H:i:s)");

        self::$aplicados[] = "check_date";
    }
}
