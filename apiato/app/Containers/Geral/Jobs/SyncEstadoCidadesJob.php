<?php

namespace App\Containers\Geral\Jobs;

use App\Ship\Parents\Jobs\Job;
use App\Containers\Geral\Data\Repositories\Cadastro\EstadoRepository;
use App\Containers\Geral\Data\Repositories\Cadastro\CidadeRepository;
use Illuminate\Support\Facades\DB;
use Exception;

class SyncEstadoCidadesJob extends Job
{
    public $tries = 5;

    private $idEstado;

    public function __construct($idEstado = null)
    {
        $this->idEstado = $idEstado;
    }

    public function handle()
    {
        if ($this->idEstado) {
            $estadoRepository = new EstadoRepository(app());
            $cidadeRepository = new CidadeRepository(app());
            $estado = $estadoRepository->find($this->idEstado);
            if ($estado &&
                ! $estado->trashed() &&
                $estado->id_ibge
            ) {
                $client = new \GuzzleHttp\Client([
                    'timeout'         => 30,
                ]);

                $response = $client->request('GET', 'https://servicodados.ibge.gov.br/api/v1/localidades/estados/'.$estado->id_ibge.'/municipios');
                if ($response) {
                    $statusCode = $response->getStatusCode();
                    $header = $response->getHeader('content-type')[0];

                    if ($statusCode == 200 &&
                        $header &&
                        stripos($header, 'application/json') >= 0
                    ) {
                        $responseBody = (string)$response->getBody();
                        if ($responseBody) {
                            $body = json_decode($responseBody, true);
                            if ($body &&
                                is_array($body)
                            ) {
                                if ($estado->cidades->count() > 0) {
                                    foreach ($estado->cidades as $cidade) {
                                        $found = false;
                                        try {
                                            foreach ($body as $row) {
                                                if ($row &&
                                                    is_array($row) &&
                                                    isset($row['id']) &&
                                                    isset($row['nome']) &&
                                                    $row['id'] &&
                                                    $row['nome']
                                                ) {
                                                    if (! isset($row['microrregiao']) ||
                                                        ! $row['microrregiao'] ||
                                                        ! is_array($row['microrregiao']) ||
                                                        ! isset($row['microrregiao']['mesorregiao']) ||
                                                        ! $row['microrregiao']['mesorregiao'] ||
                                                        ! is_array($row['microrregiao']['mesorregiao']) ||
                                                        ! isset($row['microrregiao']['mesorregiao']['UF']) ||
                                                        ! $row['microrregiao']['mesorregiao']['UF'] ||
                                                        ! is_array($row['microrregiao']['mesorregiao']['UF']) ||
                                                        ! isset($row['microrregiao']['mesorregiao']['UF']['id']) ||
                                                        $row['microrregiao']['mesorregiao']['UF']['id'] != $estado->id_ibge ||
                                                        $row['id'] == $cidade->id_ibge
                                                    ) {
                                                        $found = true;
                                                        break;
                                                    }
                                                }
                                            }
                                            if (! $found) {
                                                DB::beginTransaction();
                                                try {
                                                    if ($cidade->delete()) {
                                                        DB::commit();
                                                    } else {
                                                        DB::rollBack();
                                                    }
                                                } catch (Exception $err) {
                                                    DB::rollBack();
                                                }
                                            }
                                        } catch (Exception $err) {
                                        }
                                    }
                                }
                                foreach ($body as $row) {
                                    try {
                                        if ($row &&
                                            is_array($row) &&
                                            isset($row['id']) &&
                                            isset($row['nome']) &&
                                            $row['id'] &&
                                            $row['nome']
                                        ) {
                                            if (! isset($row['microrregiao']) ||
                                                ! $row['microrregiao'] ||
                                                ! is_array($row['microrregiao']) ||
                                                ! isset($row['microrregiao']['mesorregiao']) ||
                                                ! $row['microrregiao']['mesorregiao'] ||
                                                ! is_array($row['microrregiao']['mesorregiao']) ||
                                                ! isset($row['microrregiao']['mesorregiao']['UF']) ||
                                                ! $row['microrregiao']['mesorregiao']['UF'] ||
                                                ! is_array($row['microrregiao']['mesorregiao']['UF']) ||
                                                ! isset($row['microrregiao']['mesorregiao']['UF']['id']) ||
                                                $row['microrregiao']['mesorregiao']['UF']['id'] != $estado->id_ibge
                                            ) {
                                                continue;
                                            }
                                            $cidade = $estado->cidades()->where([
                                                'id_ibge' => $row['id']
                                            ])->first();
                                            if (! $cidade ||
                                                $cidade->trashed()
                                            ) {
                                                DB::beginTransaction();
                                                try {
                                                    $cidade = $cidadeRepository->create([
                                                        'id_estado' => $estado->id,
                                                        'nome' => $row['nome'],
                                                        'id_ibge' => $row['id'],
                                                    ]);
                                                    if ($cidade &&
                                                        $cidade->isValid() &&
                                                        $cidade->id
                                                    ) {
                                                        DB::commit();
                                                    } else {
                                                        DB::rollBack();
                                                    }
                                                } catch (Exception $err) {
                                                    DB::rollBack();
                                                }
                                            } elseif ($cidade->nome != $row['nome']) {
                                                DB::beginTransaction();
                                                try {
                                                    $cidade->fill([
                                                        'nome' => $row['nome'],
                                                    ]);
                                                    if ($cidade->saveOrFail() &&
                                                        $cidade->isValid()
                                                    ) {
                                                        DB::commit();
                                                    } else {
                                                        DB::rollBack();
                                                    }
                                                } catch (Exception $err) {
                                                    DB::rollBack();
                                                }
                                            }
                                        }
                                    } catch (Exception $err) {
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
