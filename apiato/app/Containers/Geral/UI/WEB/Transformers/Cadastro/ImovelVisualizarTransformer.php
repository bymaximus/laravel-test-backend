<?php

namespace App\Containers\Geral\UI\WEB\Transformers\Cadastro;

use App\Containers\Geral\Models\Imovel;
use App\Ship\Parents\Transformers\Transformer;

class ImovelVisualizarTransformer extends Transformer
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
            'email' => $entity->email,
            'id_estado' => ($entity->estadoAtivo ? -2 : null),
            'id_cidade' => ($entity->cidadeAtivo ? -2 : null),
            'bairro' => $entity->bairro,
            'rua' => $entity->rua,
            'numero' => $entity->numero,
            'complemento' => $entity->complemento,
        ];
    }
}
