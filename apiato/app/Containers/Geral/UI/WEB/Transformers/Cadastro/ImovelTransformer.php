<?php

namespace App\Containers\Geral\UI\WEB\Transformers\Cadastro;

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
        $idEstado = null;
        $idCidade = null;
        if ($entity->estadoAtivo) {
            if ($entity->estadoAtivo->trashed() ||
                ! $entity->estadoAtivo->id_estado ||
                (
                    $entity->estadoAtivo->estado &&
                    $entity->estadoAtivo->estado->trashed()
                )
            ) {
                $idEstado = -2;
            } else {
                $idEstado = $entity->estadoAtivo->id_estado;
            }
        }
        if ($entity->cidadeAtivo) {
            if ($entity->cidadeAtivo->trashed() ||
                ! $entity->cidadeAtivo->id_cidade ||
                (
                    $entity->cidadeAtivo->cidade &&
                    $entity->cidadeAtivo->cidade->trashed()
                )
            ) {
                $idCidade = -2;
            } elseif ($entity->cidadeAtivo->id_cidade &&
                $entity->estadoAtivo &&
                $entity->estadoAtivo->estado &&
                $entity->estadoAtivo->estado->cidades->count() > 0
            ) {
                $cidade = $entity->estadoAtivo->estado->cidades->where('id', '=', $entity->cidadeAtivo->id_cidade)->first();
                if (! $cidade ||
                    $cidade->trashed()
                ) {
                    $idCidade = -2;
                } else {
                    $idCidade = $entity->cidadeAtivo->id_cidade;
                }
            } else {
                $idCidade = $entity->cidadeAtivo->id_cidade;
            }
        }

        return [
            'email' => $entity->email,
            'id_estado' => $idEstado,
            'id_cidade' => $idCidade,
            'bairro' => $entity->bairro,
            'rua' => $entity->rua,
            'numero' => $entity->numero,
            'complemento' => $entity->complemento,
        ];
    }
}
