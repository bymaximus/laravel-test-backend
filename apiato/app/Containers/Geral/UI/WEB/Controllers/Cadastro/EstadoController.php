<?php

namespace App\Containers\Geral\UI\WEB\Controllers\Cadastro;

use App\Containers\Geral\UI\WEB\Controllers\CommonController;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Estado\ObterTodosRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Estado\AdicionarRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Estado\RemoverRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Estado\ObterRegistroRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Estado\AtualizarRegistroRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Estado\HistoricoObterTodosRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Estado\HistoricoObterRegistroRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Estado\RemovidoObterTodosRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Estado\RemovidoHistoricoObterTodosRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Estado\RemovidoHistoricoObterRegistroRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Estado\SincronizarRequest;
use App\Containers\Geral\UI\WEB\Transformers\Cadastro\EstadoTransformer;
use App\Containers\Geral\UI\WEB\Transformers\Cadastro\EstadoHistoricoTransformer;
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
 * Class EstadoController
 *
 * @package App\Containers\Geral\UI\WEB\Controllers\Cadastro
 */
class EstadoController extends CommonController
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

        return Apiato::call('Geral@Cadastro\Estado\ObterTodosAction', [$request])->editColumn('dt_criacao', function ($registro) {
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
        return Apiato::call('Geral@Cadastro\Estado\HistoricoObterTodosAction', [$request])->filterColumn('user.usuario', function ($query, $keyword) {
        })->editColumn('created_at', function ($registro) {
            return $registro->created_at ? with(new Carbon($registro->created_at))->format('d/m/Y H:i:s') : '';
        })->editColumn('event', function ($registro) {
            return $registro->evento;
        })->addColumn('user.usuario', function ($registro) {
            return $registro->user ? $registro->user->usuario : '';
        })->blacklist(['created_at', 'event'])->make(true);
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
        return Apiato::call('Geral@Cadastro\Estado\RemovidoObterTodosAction', [$request])->editColumn('dt_criacao', function ($registro) {
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
        return Apiato::call('Geral@Cadastro\Estado\RemovidoHistoricoObterTodosAction', [$request])->filterColumn('user.usuario', function ($query, $keyword) {
        })->editColumn('created_at', function ($registro) {
            return $registro->created_at ? with(new Carbon($registro->created_at))->format('d/m/Y H:i:s') : '';
        })->editColumn('event', function ($registro) {
            return $registro->evento;
        })->addColumn('user.usuario', function ($registro) {
            return $registro->user ? $registro->user->usuario : '';
        })->blacklist(['created_at', 'event'])->make(true);
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

        $model = Apiato::call('Geral@Cadastro\Estado\RemoverAction', [$request]);
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

        $model = Apiato::call('Geral@Cadastro\Estado\AdicionarAction', [$request]);
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
     * @param ObterRegistroRequest $request
     */
    public function obterRegistro(ObterRegistroRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }

        $model = Apiato::call('Geral@Cadastro\Estado\ObterRegistroAction', [$request]);
        if ($model &&
            $model->id > 0
        ) {
            $lists = [];

            $manager = new Manager();
            $manager->setSerializer(new ArraySerializer());

            $resource = new Item($model, new EstadoTransformer);
            $lists['registro'] = $manager->createData($resource)->toArray();

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

        $model = Apiato::call('Geral@Cadastro\Estado\HistoricoObterRegistroAction', [$request]);
        if ($model &&
            $model->id > 0
        ) {
            $lists = [];

            $manager = new Manager();
            $manager->setSerializer(new ArraySerializer());

            $resource = new Item($model, new EstadoHistoricoTransformer);
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

        $model = Apiato::call('Geral@Cadastro\Estado\HistoricoObterRegistroAction', [$request]);
        if ($model &&
            $model->id > 0
        ) {
            $lists = [];

            $manager = new Manager();
            $manager->setSerializer(new ArraySerializer());

            $resource = new Item($model, new EstadoHistoricoTransformer);
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

        $model = Apiato::call('Geral@Cadastro\Estado\AtualizarRegistroAction', [$request]);
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

    /**
     * Sync
     *
     * @param SincronizarRequest $request
     */
    public function sincronizar(SincronizarRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }

        $result = Apiato::call('Geral@Cadastro\Estado\SincronizarAction', [$request]);
        if ($result === true) {
            return response()->json([
                'message' => 'Tarefa agendada com sucesso.',
                'status' => 'criado',
            ], 200);
        } elseif ($result === null) {
            return response()->json([
                'message' => 'Tarefa já agendada.',
                'status' => 'existente',
            ], 200);
        } else {
            throw new CreateResourceFailedException('Tarefa não agendada.', null, 500);
        }
    }
}
