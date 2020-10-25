<?php

namespace App\Containers\Geral\UI\WEB\Controllers\Seguranca;

use App\Containers\Geral\UI\WEB\Controllers\CommonController;
use App\Containers\Geral\UI\WEB\Requests\Seguranca\Perfil\ObterTodosRequest;
use App\Containers\Geral\UI\WEB\Requests\Seguranca\Perfil\AdicionarRequest;
use App\Containers\Geral\UI\WEB\Requests\Seguranca\Perfil\RemoverRequest;
use App\Containers\Geral\UI\WEB\Requests\Seguranca\Perfil\ObterRegistroRequest;
use App\Containers\Geral\UI\WEB\Requests\Seguranca\Perfil\ObterFuncionalidadesRequest;
use App\Containers\Geral\UI\WEB\Requests\Seguranca\Perfil\AtualizarRegistroRequest;
use App\Containers\Geral\UI\WEB\Requests\Seguranca\Perfil\HistoricoObterTodosRequest;
use App\Containers\Geral\UI\WEB\Requests\Seguranca\Perfil\HistoricoObterRegistroRequest;
use App\Containers\Geral\UI\WEB\Requests\Seguranca\Perfil\RemovidoObterTodosRequest;
use App\Containers\Geral\UI\WEB\Requests\Seguranca\Perfil\RemovidoHistoricoObterTodosRequest;
use App\Containers\Geral\UI\WEB\Requests\Seguranca\Perfil\RemovidoHistoricoObterRegistroRequest;
use App\Containers\Geral\UI\WEB\Transformers\Seguranca\PerfilTransformer;
use App\Containers\Geral\UI\WEB\Transformers\Seguranca\PerfilFuncionalidadeTransformer;
use App\Containers\Geral\UI\WEB\Transformers\Seguranca\AdicionarPerfilFuncionalidadeTransformer;
use App\Containers\Geral\UI\WEB\Transformers\Seguranca\PerfilHistoricoTransformer;
use Apiato\Core\Foundation\Facades\Apiato;
use App\Ship\Exceptions\CreateResourceFailedException;
use App\Ship\Exceptions\UpdateResourceFailedException;
use App\Ship\Exceptions\DeleteResourceFailedException;
use App\Ship\Exceptions\NotFoundException;
use App\Containers\Geral\Exceptions\AuthenticationException;
use Apiato\Core\Traits\ResponseTrait;
use Carbon\Carbon;
use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use Exception;

/**
 * Class PerfilController
 *
 * @package App\Containers\Geral\UI\WEB\Controllers\Seguranca
 */
class PerfilController extends CommonController
{
    use ResponseTrait;

    /**
     * Show all entities
     *
     * @param ObterTodosRequest $request
     */
    public function index(ObterTodosRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }

        return Apiato::call('Geral@Seguranca\Perfil\ObterTodosAction', [$request])->editColumn('dt_criacao', function ($registro) {
            return $registro->dt_criacao ? with(new Carbon($registro->dt_criacao))->format('d/m/Y H:i:s') : '';
        })->editColumn('dt_alteracao', function ($registro) {
            return $registro->dt_alteracao ? with(new Carbon($registro->dt_alteracao))->format('d/m/Y H:i:s') : '';
        })->blacklist(['dt_criacao', 'dt_alteracao'])->make(true);
    }

    /**
     * Show all entities
     *
     * @param HistoricoObterTodosRequest $request
     */
    public function indexHistorico(HistoricoObterTodosRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }
        return Apiato::call('Geral@Seguranca\Perfil\HistoricoObterTodosAction', [$request])->filterColumn('user.usuario', function ($query, $keyword) {
        })->editColumn('created_at', function ($registro) {
            return $registro->created_at ? with(new Carbon($registro->created_at))->format('d/m/Y H:i:s') : '';
        })->editColumn('event', function ($registro) {
            return $registro->evento;
        })->addColumn('user.usuario', function ($registro) {
            return $registro->user ? $registro->user->usuario : '';
        })->addColumn('user.removido', function ($registro) {
            return $registro->user && $registro->user->trashed() ? true : false;
        })->blacklist(['created_at', 'event', 'user.removido'])->make(true);
    }

    /**
     * Show all entities
     *
     * @param RemovidoObterTodosRequest $request
     */
    public function indexRemovido(RemovidoObterTodosRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }
        return Apiato::call('Geral@Seguranca\Perfil\RemovidoObterTodosAction', [$request])->editColumn('dt_criacao', function ($registro) {
            return $registro->dt_criacao ? with(new Carbon($registro->dt_criacao))->format('d/m/Y H:i:s') : '';
        })->editColumn('dt_alteracao', function ($registro) {
            return $registro->dt_alteracao ? with(new Carbon($registro->dt_alteracao))->format('d/m/Y H:i:s') : '';
        })->editColumn('dt_remocao', function ($registro) {
            return $registro->dt_remocao ? with(new Carbon($registro->dt_remocao))->format('d/m/Y H:i:s') : '';
        })->blacklist(['dt_criacao', 'dt_alteracao', 'dt_remocao'])->make(true);
    }

    /**
     * Show all entities
     *
     * @param RemovidoHistoricoObterTodosRequest $request
     */
    public function indexRemovidoHistorico(RemovidoHistoricoObterTodosRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }
        return Apiato::call('Geral@Seguranca\Perfil\RemovidoHistoricoObterTodosAction', [$request])->filterColumn('user.usuario', function ($query, $keyword) {
        })->editColumn('created_at', function ($registro) {
            return $registro->created_at ? with(new Carbon($registro->created_at))->format('d/m/Y H:i:s') : '';
        })->editColumn('event', function ($registro) {
            return $registro->evento;
        })->addColumn('user.usuario', function ($registro) {
            return $registro->user ? $registro->user->usuario : '';
        })->addColumn('user.removido', function ($registro) {
            return $registro->user && $registro->user->trashed() ? true : false;
        })->blacklist(['created_at', 'event', 'user.removido'])->make(true);
    }

    /**
     * Delete
     *
     * @param RemoverRequest $request
     */
    public function remover(RemoverRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }

        $model = Apiato::call('Geral@Seguranca\Perfil\RemoverAction', [$request]);
        if ($model &&
            $model->id > 0
        ) {
            return response()->json([
                'message' => 'Registro removido com sucesso.',
                'status' => 'removido',
                'id' => $model->id
            ], 200);
        } else {
            throw new DeleteResourceFailedException('Registro não encontrado.', null, 500);
        }
    }

    /**
     * Add new record
     *
     * @param AdicionarRequest $request
     */
    public function adicionar(AdicionarRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }

        $model = Apiato::call('Geral@Seguranca\Perfil\AdicionarAction', [$request]);
        if ($model &&
            $model->id > 0
        ) {
            return response()->json([
                'message' => 'Registro adicionado com sucesso.',
                'status' => 'criado',
                'id' => $model->id
            ], 201);
        } else {
            throw new CreateResourceFailedException('Registro não criado.', null, 500);
        }
    }

    /**
     * Show record
     *
     * @param ObterFuncionalidadesRequest $request
     */
    public function obterFuncionalidades(ObterFuncionalidadesRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }

        $registros = Apiato::call('Geral@Seguranca\Perfil\ObterFuncionalidadesAction', [$request]);
        if ($registros != null &&
            (
                is_array($registros) ||
                $registros instanceof \Illuminate\Database\Eloquent\Collection
            )
        ) {
            $manager = new Manager();
            $manager->setSerializer(new ArraySerializer());
            $resource = new Collection($registros, new AdicionarPerfilFuncionalidadeTransformer);
            $registros = current($manager->createData($resource)->toArray());
        } else {
            $registros = [];
        }
        $registros = [
            [
                'codigo' => -1,
                'nome' => 'Menu Principal',
                'icon' => 'icon-root',
                'opened' => true,
                'selected' => true,
                'disabled' => true,
                'children' => $registros
            ]
        ];
        $lists = [
            'funcionalidades' => $registros,
        ];
        return $this->json($lists);
    }

    /**
     * Show record
     *
     * @param ObterRegistroRequest $request
     */
    public function obterRegistro(ObterRegistroRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }

        $model = Apiato::call('Geral@Seguranca\Perfil\ObterRegistroAction', [$request]);
        if ($model &&
            $model->id > 0
        ) {
            $lists = [];

            $manager = new Manager();
            $manager->setSerializer(new ArraySerializer());

            $resource = new Item($model, new PerfilTransformer);
            $lists['registro'] = $manager->createData($resource)->toArray();

            $resource = new Collection($model->funcionalidades, new PerfilFuncionalidadeTransformer);
            $registros = current($manager->createData($resource)->toArray());
            $registros = [
                [
                    'codigo' => -1,
                    'nome' => 'Menu Principal',
                    'icon' => 'icon-root',
                    'opened' => true,
                    'selected' => true,
                    'disabled' => true,
                    'children' => $registros
                ]
            ];
            $lists['funcionalidades'] = $registros;

            return $this->json($lists);
        } else {
            throw new NotFoundException();
        }
    }

    /**
     * Show record
     *
     * @param HistoricoObterRegistroRequest $request
     */
    public function obterHistoricoRegistro(HistoricoObterRegistroRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }

        $model = Apiato::call('Geral@Seguranca\Perfil\HistoricoObterRegistroAction', [$request]);
        if ($model &&
            $model->id > 0
        ) {
            $lists = [];

            $manager = new Manager();
            $manager->setSerializer(new ArraySerializer());

            $resource = new Item($model, new PerfilHistoricoTransformer);
            $lists['registro'] = $manager->createData($resource)->toArray();

            return $this->json($lists);
        } else {
            throw new NotFoundException();
        }
    }

    /**
     * Show record
     *
     * @param RemovidoHistoricoObterRegistroRequest $request
     */
    public function obterRemovidoHistoricoRegistro(RemovidoHistoricoObterRegistroRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }

        $model = Apiato::call('Geral@Seguranca\Perfil\HistoricoObterRegistroAction', [$request]);
        if ($model &&
            $model->id > 0
        ) {
            $lists = [];

            $manager = new Manager();
            $manager->setSerializer(new ArraySerializer());

            $resource = new Item($model, new PerfilHistoricoTransformer);
            $lists['registro'] = $manager->createData($resource)->toArray();

            return $this->json($lists);
        } else {
            throw new NotFoundException();
        }
    }

    /**
     * Update
     *
     * @param AtualizarRegistroRequest $request
     */
    public function atualizar(AtualizarRegistroRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }

        $model = Apiato::call('Geral@Seguranca\Perfil\AtualizarRegistroAction', [$request]);
        if ($model &&
            $model->id > 0
        ) {
            return response()->json([
                'message' => 'Registro atualizado com sucesso.',
                'status' => 'atualizado',
                'id' => $model->id
            ], 200);
        } else {
            throw new UpdateResourceFailedException('Registro não encontrado.', null, 500);
        }
    }
}
