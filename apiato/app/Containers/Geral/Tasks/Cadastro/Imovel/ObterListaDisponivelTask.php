<?php

namespace App\Containers\Geral\Tasks\Cadastro\Imovel;

use App\Containers\Geral\Data\Repositories\Cadastro\ImovelRepository;
use App\Ship\Parents\Tasks\Task;

class ObterListaDisponivelTask extends Task
{
    protected $repository;

    public function __construct(ImovelRepository $repository)
    {
        $this->repository = $repository;
    }

    public function run()
    {
        return $this->repository->makeModel()->disponivel()->select('id', 'rua', 'numero', 'complemento', 'bairro')->orderByRaw('rua, numero, complemento, bairro ASC')->get();
    }
}
