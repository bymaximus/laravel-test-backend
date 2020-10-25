<?php

namespace App\Containers\Geral\UI\WEB\Controllers\Cadastro;

use App\Containers\Geral\UI\WEB\Controllers\CommonController;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Contrato\ObterTodosRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Contrato\AdicionarRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Contrato\RemoverRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Contrato\ObterRegistroRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Contrato\ObterListaDisponivelRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Contrato\ObterListaContratadoRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Contrato\AtualizarRegistroRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Contrato\HistoricoObterTodosRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Contrato\HistoricoObterRegistroRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Contrato\RemovidoObterTodosRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Contrato\RemovidoHistoricoObterTodosRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Contrato\RemovidoHistoricoObterRegistroRequest;
use App\Containers\Geral\UI\WEB\Transformers\Cadastro\ContratoTransformer;
use App\Containers\Geral\UI\WEB\Transformers\Cadastro\ContratoHistoricoTransformer;
use App\Containers\Geral\UI\WEB\Transformers\ImovelTransformer;
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
 * Class ContratoController
 *
 * @package App\Containers\Geral\UI\WEB\Controllers\Cadastro
 */
class ContratoController extends CommonController
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

        $imovelFiltrado = false;
        return Apiato::call('Geral@Cadastro\Contrato\ObterTodosAction', [$request])->filterColumn('id_imovel', function ($query, $keyword) use (&$imovelFiltrado) {
            if (request()->has('columns.1.search.value')) {
                $requestKeyword = request()->input('columns.1.search.value');
                if ($requestKeyword &&
                    is_numeric($requestKeyword) &&
                    $requestKeyword == $keyword &&
                    !$imovelFiltrado
                ) {
                    $imovelFiltrado = true;
                    $query->where('id_imovel', '=', $keyword);
                }
            }
        })->addColumn('endereco', function ($registro) {
            return $registro->imovel ? $registro->imovel->endereco_contrato : '';
        })->blacklist(['endereco'])->make(true);
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
        return Apiato::call('Geral@Cadastro\Contrato\HistoricoObterTodosAction', [$request])->filterColumn('user.usuario', function ($query, $keyword) {
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
        return Apiato::call('Geral@Cadastro\Contrato\RemovidoObterTodosAction', [$request])->editColumn('dt_criacao', function ($registro) {
            return $registro->dt_criacao ? with(new Carbon($registro->dt_criacao))->format('d/m/Y H:i:s') : '';
        })->editColumn('dt_alteracao', function ($registro) {
            return $registro->dt_alteracao ? with(new Carbon($registro->dt_alteracao))->format('d/m/Y H:i:s') : '';
        })->editColumn('dt_remocao', function ($registro) {
            return $registro->dt_remocao ? with(new Carbon($registro->dt_remocao))->format('d/m/Y H:i:s') : '';
        })->addColumn('endereco', function ($registro) {
            return $registro->imovel ? $registro->imovel->endereco_contrato : '';
        })->blacklist(['dt_criacao', 'dt_alteracao', 'dt_remocao', 'endereco'])->make(true);
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
        return Apiato::call('Geral@Cadastro\Contrato\RemovidoHistoricoObterTodosAction', [$request])->filterColumn('user.usuario', function ($query, $keyword) {
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

        $model = Apiato::call('Geral@Cadastro\Contrato\RemoverAction', [$request]);
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

        $model = Apiato::call('Geral@Cadastro\Contrato\AdicionarAction', [$request]);
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

        $model = Apiato::call('Geral@Cadastro\Contrato\ObterRegistroAction', [$request]);
        if ($model &&
            $model->id > 0
        ) {
            $lists = [];
            $lists['imoveis'] = [];

            $manager = new Manager();
            $manager->setSerializer(new ArraySerializer());

            $registros = Apiato::call('Geral@Cadastro\Imovel\ObterListaDisponivelAction', [$request]);
            if (! $registros) {
                $registros = new \Illuminate\Database\Eloquent\Collection([]);
            }
            if ($model->imovel) {
                $imovel = $registros->where('id', '=', $model->imovel->id)->first();
                if (! $imovel ||
                    $imovel->trashed()
                ) {
                    $registros->push($model->imovel);
                }
            }
            $resource = new Collection($registros, new ImovelTransformer);
            $lists['imoveis'] = current($manager->createData($resource)->toArray());

            $resource = new Item($model, new ContratoTransformer);
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

        $model = Apiato::call('Geral@Cadastro\Contrato\HistoricoObterRegistroAction', [$request]);
        if ($model &&
            $model->id > 0
        ) {
            $lists = [];

            $manager = new Manager();
            $manager->setSerializer(new ArraySerializer());

            $resource = new Item($model, new ContratoHistoricoTransformer);
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

        $model = Apiato::call('Geral@Cadastro\Contrato\HistoricoObterRegistroAction', [$request]);
        if ($model &&
            $model->id > 0
        ) {
            $lists = [];

            $manager = new Manager();
            $manager->setSerializer(new ArraySerializer());

            $resource = new Item($model, new ContratoHistoricoTransformer);
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

        $model = Apiato::call('Geral@Cadastro\Contrato\AtualizarRegistroAction', [$request]);
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
     * @param ObterListaContratadoRequest $request
     */
    public function obterListaContratado(ObterListaContratadoRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }

        $manager = new Manager();
        $manager->setSerializer(new ArraySerializer());
        $lists = [];
        $lists['imoveis'] = [];

        $registros = Apiato::call('Geral@Cadastro\Imovel\ObterListaContratadoAction', [$request]);
        if ($registros) {
            $resource = new Collection($registros, new ImovelTransformer);
            $lists['imoveis'] = current($manager->createData($resource)->toArray());
        }

        return $this->json($lists);
    }

    /**
     * Show record
     *
     * @param ObterListaDisponivelRequest $request
     */
    public function obterListaDisponivel(ObterListaDisponivelRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }

        $manager = new Manager();
        $manager->setSerializer(new ArraySerializer());
        $lists = [];
        $lists['imoveis'] = [];

        $registros = Apiato::call('Geral@Cadastro\Imovel\ObterListaDisponivelAction', [$request]);
        if ($registros) {
            $resource = new Collection($registros, new ImovelTransformer);
            $lists['imoveis'] = current($manager->createData($resource)->toArray());
        }

        return $this->json($lists);
    }
}
