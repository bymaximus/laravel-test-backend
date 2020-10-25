<?php

namespace App\Containers\Geral\UI\WEB\Transformers\Cadastro;

use App\Containers\Geral\Models\Contrato;
use App\Ship\Parents\Transformers\Transformer;

class ContratoTransformer extends Transformer
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
     * @param Contrato $entity
     * @return array
     */
    public function transform(Contrato $entity)
    {
        return [
            'id_imovel' => $entity->id_imovel,
            'email' => $entity->email,
            'nome' => $entity->nome,
            'tipo_pessoa' => $entity->tipo_pessoa,
            //'documento' => $entity->documento_formatado,
            'documento' => $entity->documento,
        ];
    }
}
