<?php

namespace App\API\Controller;

use App\Model\DB\Modalidade;

class Modalidades {

    public function listar() {
        $modalidade = new Modalidade();
        return $modalidade->listar();
    }

}