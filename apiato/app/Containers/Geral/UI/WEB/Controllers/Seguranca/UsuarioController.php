<?php

namespace App\Containers\Geral\UI\WEB\Controllers\Seguranca;

use App\Containers\Geral\UI\WEB\Controllers\CommonController;
use App\Containers\Geral\UI\WEB\Requests\Seguranca\Usuario\ObterTodosRequest;
use App\Containers\Geral\UI\WEB\Requests\Seguranca\Usuario\AdicionarRequest;
use App\Containers\Geral\UI\WEB\Requests\Seguranca\Usuario\RemoverRequest;
use App\Containers\Geral\UI\WEB\Requests\Seguranca\Usuario\ObterRegistroRequest;
use App\Containers\Geral\UI\WEB\Requests\Seguranca\Usuario\ObterPerfisRequest;
use App\Containers\Geral\UI\WEB\Requests\Seguranca\Usuario\AtualizarRegistroRequest;
use App\Containers\Geral\UI\WEB\Requests\Seguranca\Usuario\HistoricoObterTodosRequest;
use App\Containers\Geral\UI\WEB\Requests\Seguranca\Usuario\HistoricoObterRegistroRequest;
use App\Containers\Geral\UI\WEB\Requests\Seguranca\Usuario\RemovidoObterTodosRequest;
use App\Containers\Geral\UI\WEB\Requests\Seguranca\Usuario\RemovidoHistoricoObterTodosRequest;
use App\Containers\Geral\UI\WEB\Requests\Seguranca\Usuario\RemovidoHistoricoObterRegistroRequest;
use App\Containers\Geral\UI\WEB\Transformers\Seguranca\UsuarioTransformer;
use App\Containers\Geral\UI\WEB\Transformers\Seguranca\UsuarioPerfilTransformer;
use App\Containers\Geral\UI\WEB\Transformers\Seguranca\UsuarioHistoricoTransformer;
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
 * Class UsuarioController
 *
 * @package App\Containers\Geral\UI\WEB\Controllers\Seguranca
 */
class UsuarioController extends CommonController
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

        return Apiato::call('Geral@Seguranca\Usuario\ObterTodosAction', [$request])->editColumn('dt_criacao', function ($registro) {
            return $registro->dt_criacao ? with(new Carbon($registro->dt_criacao))->format('d/m/Y H:i:s') : '';
        })->editColumn('dt_alteracao', function ($registro) {
            return $registro->dt_alteracao ? with(new Carbon($registro->dt_alteracao))->format('d/m/Y H:i:s') : '';
        })->editColumn('dt_acesso', function ($registro) {
            return $registro->dt_acesso ? with(new Carbon($registro->dt_acesso))->format('d/m/Y H:i:s') : '';
        })->editColumn('id_perfil', function ($registro) {
            return $registro->id_perfil && $registro->perfil && $registro->perfil->nome ? $registro->perfil->nome : '';
        })->addColumn('perfil.removido', function ($registro) {
            return $registro->id_perfil && $registro->perfil && $registro->perfil->trashed() ? true : false;
        })->blacklist(['dt_criacao', 'dt_alteracao', 'dt_acesso', 'id_perfil', 'perfil.removido'])->make(true);
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
        return Apiato::call('Geral@Seguranca\Usuario\HistoricoObterTodosAction', [$request])->filterColumn('user.usuario', function ($query, $keyword) {
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
        return Apiato::call('Geral@Seguranca\Usuario\RemovidoObterTodosAction', [$request])->editColumn('dt_criacao', function ($registro) {
            return $registro->dt_criacao ? with(new Carbon($registro->dt_criacao))->format('d/m/Y H:i:s') : '';
        })->editColumn('dt_alteracao', function ($registro) {
            return $registro->dt_alteracao ? with(new Carbon($registro->dt_alteracao))->format('d/m/Y H:i:s') : '';
        })->editColumn('dt_acesso', function ($registro) {
            return $registro->dt_acesso ? with(new Carbon($registro->dt_acesso))->format('d/m/Y H:i:s') : '';
        })->editColumn('dt_remocao', function ($registro) {
            return $registro->dt_remocao ? with(new Carbon($registro->dt_remocao))->format('d/m/Y H:i:s') : '';
        })->editColumn('id_perfil', function ($registro) {
            return $registro->id_perfil && $registro->perfil && $registro->perfil->nome ? $registro->perfil->nome : '';
        })->addColumn('perfil.removido', function ($registro) {
            return $registro->id_perfil && $registro->perfil && $registro->perfil->trashed() ? true : false;
        })->blacklist(['dt_criacao', 'dt_alteracao', 'dt_acesso', 'id_perfil', 'dt_remocao', 'perfil.removido'])->make(true);
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
        return Apiato::call('Geral@Seguranca\Usuario\RemovidoHistoricoObterTodosAction', [$request])->filterColumn('user.usuario', function ($query, $keyword) {
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

        $model = Apiato::call('Geral@Seguranca\Usuario\RemoverAction', [$request]);
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

        $model = Apiato::call('Geral@Seguranca\Usuario\AdicionarAction', [$request]);
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
     * @param ObterPerfisRequest $request
     */
    public function obterPerfis(ObterPerfisRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }

        $manager = new Manager();
        $manager->setSerializer(new ArraySerializer());
        $lists = [];
        $lists['perfis'] = [];

        $registros = Apiato::call('Geral@Seguranca\Perfil\ObterListaAction', [$request]);
        if ($registros) {
            $resource = new Collection($registros, new UsuarioPerfilTransformer);
            $lists['perfis'] = current($manager->createData($resource)->toArray());
        }

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

        $model = Apiato::call('Geral@Seguranca\Usuario\ObterRegistroAction', [$request]);
        if ($model &&
            $model->id > 0
        ) {
            $lists = [];

            $manager = new Manager();
            $manager->setSerializer(new ArraySerializer());

            $resource = new Item($model, new UsuarioTransformer);
            $lists['registro'] = $manager->createData($resource)->toArray();

            $registros = Apiato::call('Geral@Seguranca\Perfil\ObterListaAction', [$request]);
            $resource = new Collection($registros, new UsuarioPerfilTransformer);
            $lists['perfis'] = current($manager->createData($resource)->toArray());

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

        $model = Apiato::call('Geral@Seguranca\Usuario\HistoricoObterRegistroAction', [$request]);
        if ($model &&
            $model->id > 0
        ) {
            $lists = [];

            $manager = new Manager();
            $manager->setSerializer(new ArraySerializer());

            $resource = new Item($model, new UsuarioHistoricoTransformer);
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

        $model = Apiato::call('Geral@Seguranca\Usuario\HistoricoObterRegistroAction', [$request]);
        if ($model &&
            $model->id > 0
        ) {
            $lists = [];

            $manager = new Manager();
            $manager->setSerializer(new ArraySerializer());

            $resource = new Item($model, new UsuarioHistoricoTransformer);
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

        $model = Apiato::call('Geral@Seguranca\Usuario\AtualizarRegistroAction', [$request]);
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
