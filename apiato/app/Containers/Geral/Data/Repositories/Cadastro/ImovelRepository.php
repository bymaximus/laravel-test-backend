<?php

namespace App\Containers\Geral\Data\Repositories\Cadastro;

use App\Ship\Parents\Repositories\Repository;

/**
 * Class ImovelRepository
 */
class ImovelRepository extends Repository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id' => '=',
    ];
}
