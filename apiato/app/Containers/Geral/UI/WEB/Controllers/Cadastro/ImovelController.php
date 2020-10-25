<?php

namespace App\Containers\Geral\UI\WEB\Controllers\Cadastro;

use App\Containers\Geral\UI\WEB\Controllers\CommonController;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Imovel\ObterTodosRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Imovel\AdicionarRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Imovel\RemoverRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Imovel\ObterRegistroRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Imovel\ObterVisualizarRegistroRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Imovel\AtualizarRegistroRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Imovel\HistoricoObterTodosRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Imovel\HistoricoObterRegistroRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Imovel\RemovidoObterTodosRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Imovel\RemovidoHistoricoObterTodosRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Imovel\RemovidoHistoricoObterRegistroRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Imovel\ObterListasRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Imovel\ObterCidadesRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Imovel\ObterRegistroCidadesRequest;
use App\Containers\Geral\UI\WEB\Transformers\Cadastro\ImovelTransformer;
use App\Containers\Geral\UI\WEB\Transformers\Cadastro\ImovelVisualizarTransformer;
use App\Containers\Geral\UI\WEB\Transformers\Cadastro\ImovelHistoricoTransformer;
use App\Containers\Geral\UI\WEB\Transformers\EstadoTransformer;
use App\Containers\Geral\UI\WEB\Transformers\CidadeTransformer;
use App\Containers\Geral\Models\Estado;
use App\Containers\Geral\Models\Cidade;
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
 * Class ImovelController
 *
 * @package App\Containers\Geral\UI\WEB\Controllers\Cadastro
 */
class ImovelController extends CommonController
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

        $estadoFiltrado = false;
        return Apiato::call('Geral@Cadastro\Imovel\ObterTodosAction', [$request])->filterColumn('id_estado_ativo', function ($query, $keyword) use (&$estadoFiltrado) {
            if (request()->has('columns.1.search.value')) {
                $requestKeyword = request()->input('columns.1.search.value');
                if ($requestKeyword &&
                    is_numeric($requestKeyword) &&
                    $requestKeyword == $keyword &&
                    !$estadoFiltrado
                ) {
                    $estadoFiltrado = true;
                    $estado = Apiato::call('Geral@Cadastro\Estado\ObterRegistroTask', [(int)$keyword], ['removeRequestCriteria']);
                    if ($estado &&
                        $estado->id &&
                        ! $estado->trashed() &&
                        $estado->ativo &&
                        $estado->ativo->id &&
                        !$estado->ativo->trashed()
                    ) {
                        $query->where('id_estado_ativo', '=', $estado->ativo->id);
                    }
                }
            }
        })->addColumn('endereco', function ($registro) {
            return $registro->endereco ? $registro->endereco : '';
        })->addColumn('status', function ($registro) {
            return $registro->status;
        })->addColumn('bloqueado', function ($registro) {
            return $registro->bloqueado;
        })->blacklist(['endereco', 'bloqueado'])->make(true);
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
        return Apiato::call('Geral@Cadastro\Imovel\HistoricoObterTodosAction', [$request])->filterColumn('user.usuario', function ($query, $keyword) {
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
        $estadoFiltrado = false;
        return Apiato::call('Geral@Cadastro\Imovel\RemovidoObterTodosAction', [$request])->filterColumn('id_estado_ativo', function ($query, $keyword) use (&$estadoFiltrado) {
            if (request()->has('columns.1.search.value')) {
                $requestKeyword = request()->input('columns.1.search.value');
                if ($requestKeyword &&
                    is_numeric($requestKeyword) &&
                    $requestKeyword == $keyword &&
                    !$estadoFiltrado
                ) {
                    $estadoFiltrado = true;
                    $estado = Apiato::call('Geral@Cadastro\Estado\ObterRegistroTask', [(int)$keyword], ['removeRequestCriteria']);
                    if ($estado &&
                        $estado->id &&
                        ! $estado->trashed() &&
                        $estado->ativo &&
                        $estado->ativo->id &&
                        !$estado->ativo->trashed()
                    ) {
                        $query->where('id_estado_ativo', '=', $estado->ativo->id);
                    }
                }
            }
        })->addColumn('endereco', function ($registro) {
            return $registro->endereco ? $registro->endereco : '';
        })->addColumn('status', function ($registro) {
            return $registro->status;
        })->editColumn('dt_remocao', function ($registro) {
            return $registro->dt_remocao ? with(new Carbon($registro->dt_remocao))->format('d/m/Y H:i:s') : '';
        })->blacklist(['dt_remocao', 'endereco', 'bloqueado'])->make(true);
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
        return Apiato::call('Geral@Cadastro\Imovel\RemovidoHistoricoObterTodosAction', [$request])->filterColumn('user.usuario', function ($query, $keyword) {
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

        $model = Apiato::call('Geral@Cadastro\Imovel\RemoverAction', [$request]);
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

        $model = Apiato::call('Geral@Cadastro\Imovel\AdicionarAction', [$request]);
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

        $model = Apiato::call('Geral@Cadastro\Imovel\ObterRegistroAction', [$request]);
        if ($model &&
            $model->id > 0
        ) {
            if ($model->bloqueado) {
                throw new NotFoundException('Não é possível alterar um imóvel que tenha um contrato ativo.', null, 500);
            }

            $lists = [];
            $lists['estados'] = [];
            $lists['cidades'] = [];

            $manager = new Manager();
            $manager->setSerializer(new ArraySerializer());

            $registros = Apiato::call('Geral@Cadastro\Estado\ObterListaAction', [$request]);
            if (! $registros) {
                $registros = new \Illuminate\Database\Eloquent\Collection([]);
            }
            if ($model->estadoAtivo &&
                (
                    $model->estadoAtivo->trashed() ||
                    (
                        $model->estadoAtivo->estado &&
                        $model->estadoAtivo->estado->trashed()
                    )
                )
            ) {
                $registros->push(new Estado([
                    'id' => -2,
                    'nome' => $model->estadoAtivo->nome,
                    'sigla' => $model->estadoAtivo->sigla,
                ]));
            }
            if ($registros) {
                $resource = new Collection($registros, new EstadoTransformer);
                $lists['estados'] = current($manager->createData($resource)->toArray());
            }

            if ($model->estadoAtivo &&
                $model->estadoAtivo->estado
            ) {
                if (! $model->estadoAtivo->estado->cidades->count()) {
                    $registros = new \Illuminate\Database\Eloquent\Collection([]);
                } else {
                    $registros = $model->estadoAtivo->estado->cidades;
                }
                if ($model->cidadeAtivo) {
                    if ($model->cidadeAtivo->trashed() ||
                        (
                            $model->cidadeAtivo->cidade &&
                            $model->cidadeAtivo->cidade->trashed()
                        )
                    ) {
                        $registros->push(new Cidade([
                            'id' => -2,
                            'nome' => $model->cidadeAtivo->nome,
                        ]));
                    } elseif ($model->cidadeAtivo->id_cidade) {
                        $cidade = $registros->where('id', '=', $model->cidadeAtivo->id_cidade)->first();
                        if (! $cidade ||
                            $cidade->trashed()
                        ) {
                            $registros->push(new Cidade([
                                'id' => -2,
                                'nome' => $model->cidadeAtivo->nome,
                            ]));
                        }
                    }
                }
                $resource = new Collection($registros, new CidadeTransformer);
                $lists['cidades'] = current($manager->createData($resource)->toArray());
            }

            $resource = new Item($model, new ImovelTransformer);
            $lists['registro'] = $manager->createData($resource)->toArray();

            return $this->json($lists);
        } else {
            throw new NotFoundException();
        }
    }

    /**
      * Show record
      *
      * @param ObterVisualizarRegistroRequest $request
      */
    public function obterVisualizarRegistro(ObterVisualizarRegistroRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }

        $model = Apiato::call('Geral@Cadastro\Imovel\ObterRegistroAction', [$request]);
        if ($model &&
            $model->id > 0 &&
            $model->bloqueado
        ) {
            $lists = [];
            $lists['estados'] = [];
            $lists['cidades'] = [];

            $manager = new Manager();
            $manager->setSerializer(new ArraySerializer());

            $registros = new \Illuminate\Database\Eloquent\Collection([]);
            if ($model->estadoAtivo) {
                $registros->push(new Estado([
                    'id' => -2,
                    'nome' => $model->estadoAtivo->nome,
                    'sigla' => $model->estadoAtivo->sigla,
                ]));
            }
            $resource = new Collection($registros, new EstadoTransformer);
            $lists['estados'] = current($manager->createData($resource)->toArray());

            $registros = new \Illuminate\Database\Eloquent\Collection([]);
            if ($model->cidadeAtivo) {
                $registros->push(new Cidade([
                    'id' => -2,
                    'nome' => $model->cidadeAtivo->nome,
                ]));
            }
            $resource = new Collection($registros, new CidadeTransformer);
            $lists['cidades'] = current($manager->createData($resource)->toArray());

            $resource = new Item($model, new ImovelVisualizarTransformer);
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

        $model = Apiato::call('Geral@Cadastro\Imovel\HistoricoObterRegistroAction', [$request]);
        if ($model &&
            $model->id > 0
        ) {
            $lists = [];

            $manager = new Manager();
            $manager->setSerializer(new ArraySerializer());

            $resource = new Item($model, new ImovelHistoricoTransformer);
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

        $model = Apiato::call('Geral@Cadastro\Imovel\HistoricoObterRegistroAction', [$request]);
        if ($model &&
            $model->id > 0
        ) {
            $lists = [];

            $manager = new Manager();
            $manager->setSerializer(new ArraySerializer());

            $resource = new Item($model, new ImovelHistoricoTransformer);
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

        $model = Apiato::call('Geral@Cadastro\Imovel\AtualizarRegistroAction', [$request]);
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
     * Show record
     *
     * @param ObterListasRequest $request
     */
    public function obterListas(ObterListasRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }

        $manager = new Manager();
        $manager->setSerializer(new ArraySerializer());
        $lists = [];
        $lists['estados'] = [];

        $registros = Apiato::call('Geral@Cadastro\Estado\ObterListaAction', [$request]);
        if ($registros) {
            $resource = new Collection($registros, new EstadoTransformer);
            $lists['estados'] = current($manager->createData($resource)->toArray());
        }

        return $this->json($lists);
    }

    /**
     * Show record
     *
     * @param ObterCidadesRequest $request
     */
    public function obterCidades(ObterCidadesRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }

        $manager = new Manager();
        $manager->setSerializer(new ArraySerializer());
        $lists = [];
        $lists['cidades'] = [];

        $estado = Apiato::call('Geral@Cadastro\Estado\ObterRegistroAction', [$request]);
        if ($estado &&
            $estado->cidades->count() > 0
        ) {
            $resource = new Collection($estado->cidades, new CidadeTransformer);
            $lists['cidades'] = current($manager->createData($resource)->toArray());
        }

        return $this->json($lists);
    }

    /**
     * Show record
     *
     * @param ObterRegistroCidadesRequest $request
     */
    public function obterRegistroCidades(ObterRegistroCidadesRequest $request)
    {
        $registroRequest = ObterRegistroRequest::injectData([], $request->user());
        $registroRequest->id = $request->id_imovel;
        $model = Apiato::call('Geral@Cadastro\Imovel\ObterRegistroAction', [$registroRequest]);
        if ($model &&
            $model->id > 0
        ) {
            $lists = [];
            $lists['cidades'] = [];

            $manager = new Manager();
            $manager->setSerializer(new ArraySerializer());

            $estado = Apiato::call('Geral@Cadastro\Estado\ObterRegistroAction', [$request]);
            if ($estado) {
                if (! $estado->cidades->count()) {
                    $registros = new \Illuminate\Database\Eloquent\Collection([]);
                } else {
                    $registros = $estado->cidades;
                }
                if ($model->estadoAtivo &&
                    $model->cidadeAtivo &&
                    $model->estadoAtivo->id_estado &&
                    $model->estadoAtivo->id_estado == $estado->id
                ) {
                    if ($model->cidadeAtivo->trashed() ||
                        (
                            $model->cidadeAtivo->cidade &&
                            $model->cidadeAtivo->cidade->trashed()
                        )
                    ) {
                        $registros->push(new Cidade([
                            'id' => -2,
                            'nome' => $model->cidadeAtivo->nome,
                        ]));
                    } elseif ($model->cidadeAtivo->id_cidade) {
                        $cidade = $registros->where('id', '=', $model->cidadeAtivo->id_cidade)->first();
                        if (! $cidade ||
                            $cidade->trashed()
                        ) {
                            $registros->push(new Cidade([
                                'id' => -2,
                                'nome' => $model->cidadeAtivo->nome,
                            ]));
                        }
                    }
                }
                $resource = new Collection($registros, new CidadeTransformer);
                $lists['cidades'] = current($manager->createData($resource)->toArray());
            }

            return $this->json($lists);
        } else {
            throw new NotFoundException();
        }
    }
}
