<?php

namespace App\Containers\Geral\UI\WEB\Transformers\Cadastro;

use App\Containers\Geral\Models\Cidade;
use App\Ship\Parents\Transformers\Transformer;

class CidadeTransformer extends Transformer
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
     * @param Cidade $entity
     * @return array
     */
    public function transform(Cidade $entity)
    {
        return [
            'nome' => $entity->nome,
            'id_estado' => $entity->id_estado,
        ];
    }
}
