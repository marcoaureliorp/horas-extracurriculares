<?php

namespace App\API;

use Blazar\Component\WebService\WebService;
use App\Helpers\GUMPAddValidator;

class Controller extends WebService {

    public function __construct() {
        GUMPAddValidator::addPreDefinidos();

        parent::__construct(true, "acao");
    }

}
