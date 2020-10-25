<?php

namespace App\Containers\Geral\UI\WEB\Transformers;

use App\Containers\Geral\Models\Estado;
use App\Ship\Parents\Transformers\Transformer;

class EstadoTransformer extends Transformer
{
    /**
     * @var  array
     */
    protected $defaultIncludes = [
    ];

    /**
     * @var  array
     */
    protected $availableIncludes = [
    ];

    /**
     * @param Estado $entity
     * @return array
     */
    public function transform(Estado $entity)
    {
        return $entity->toArray();
    }
}
