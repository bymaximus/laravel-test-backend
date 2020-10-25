<?php

namespace App\Containers\Geral\UI\WEB\Transformers;

use App\Containers\Geral\Models\Imovel;
use App\Ship\Parents\Transformers\Transformer;

class ImovelTransformer extends Transformer
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
     * @param Imovel $entity
     * @return array
     */
    public function transform(Imovel $entity)
    {
        return [
            'id' => $entity->id,
            'endereco' => $entity->endereco_contrato,
        ];
    }
}
