<?php

namespace App\Containers\Geral\Tasks\Seguranca\Usuario;

use App\Containers\Geral\Data\Repositories\Seguranca\UsuarioRepository;
use App\Ship\Parents\Tasks\Task;
use App\Ship\Exceptions\NotFoundException;
use Illuminate\Database\Eloquent\Builder;

class HistoricoObterTodosTask extends Task
{
    protected $repository;

    public function __construct(UsuarioRepository $repository)
    {
        $this->repository = $repository;
    }

    public function run($model, $request)
    {
        if (!$model) {
            throw new NotFoundException('Registro não encontrado.');
        }
        return $model->audits()->whereHasMorph('user', 'App\Containers\Geral\Models\Usuario', function (Builder $query, $type) use ($request) {
            $query->withTrashed();
            if ($request->has('search') &&
                $request->get('search') &&
                is_array($request->get('search')) &&
                isset($request->get('search')['value']) &&
                ($keyword = $request->get('search')['value'])
            ) {
                $query->where('usuario.usuario', 'like', $keyword . '%');
            }
        });
    }
}
