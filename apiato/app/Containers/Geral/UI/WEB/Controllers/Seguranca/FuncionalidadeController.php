<?php

namespace App\Containers\Geral\UI\WEB\Controllers\Seguranca;

use App\Containers\Geral\UI\WEB\Controllers\CommonController;
use App\Containers\Geral\UI\WEB\Requests\Seguranca\Funcionalidade\ObterTodosRequest;
use App\Containers\Geral\UI\WEB\Requests\Seguranca\Funcionalidade\AtualizarNomeRequest;
use App\Containers\Geral\UI\WEB\Requests\Seguranca\Funcionalidade\AtualizarUrlRequest;
use App\Containers\Geral\UI\WEB\Requests\Seguranca\Funcionalidade\AtualizarIconeRequest;
use App\Containers\Geral\UI\WEB\Requests\Seguranca\Funcionalidade\RemoverRequest;
use App\Containers\Geral\UI\WEB\Requests\Seguranca\Funcionalidade\AdicionarRequest;
use App\Containers\Geral\UI\WEB\Transformers\Seguranca\FuncionalidadeTransformer;
use Apiato\Core\Foundation\Facades\Apiato;
use App\Ship\Exceptions\CreateResourceFailedException;
use App\Ship\Exceptions\UpdateResourceFailedException;
use App\Ship\Exceptions\DeleteResourceFailedException;
use App\Containers\Geral\Exceptions\AuthenticationException;
use Apiato\Core\Traits\ResponseTrait;
use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;

/**
 * Class FuncionalidadeController
 *
 * @package App\Containers\Geral\UI\WEB\Controllers\Seguranca
 */
class FuncionalidadeController extends CommonController
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

        $registros = Apiato::call('Geral@Seguranca\Funcionalidade\ObterTodosAction', [$request]);
        if ($registros != null &&
            (
                is_array($registros) ||
                $registros instanceof \Illuminate\Database\Eloquent\Collection
            )
        ) {
            $manager = new Manager();
            $manager->setSerializer(new ArraySerializer());
            $resource = new Collection($registros, new FuncionalidadeTransformer);
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
                'children' => $registros
            ]
        ];
        $lists = [
            'funcionalidades' => $registros
        ];
        return $this->json($lists);
    }

    /**
     * Update
     *
     * @param AtualizarNomeRequest $request
     */
    public function atualizarNome(AtualizarNomeRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }

        $model = Apiato::call('Geral@Seguranca\Funcionalidade\AtualizarNomeAction', [$request]);
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
     * Update
     *
     * @param AtualizarUrlRequest $request
     */
    public function atualizarUrl(AtualizarUrlRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }

        $model = Apiato::call('Geral@Seguranca\Funcionalidade\AtualizarUrlAction', [$request]);
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
     * Update
     *
     * @param AtualizarIconeRequest $request
     */
    public function atualizarIcone(AtualizarIconeRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }

        $model = Apiato::call('Geral@Seguranca\Funcionalidade\AtualizarIconeAction', [$request]);
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
     * Delete
     *
     * @param RemoverRequest $request
     */
    public function remover(RemoverRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }

        $model = Apiato::call('Geral@Seguranca\Funcionalidade\RemoverAction', [$request]);
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

        $model = Apiato::call('Geral@Seguranca\Funcionalidade\AdicionarAction', [$request]);
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
}
