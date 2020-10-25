<?php

namespace App\Containers\Geral\Data\Repositories\Cadastro;

use App\Ship\Parents\Repositories\Repository;

/**
 * Class ContratoRepository
 */
class ContratoRepository extends Repository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id' => '=',
    ];
}
