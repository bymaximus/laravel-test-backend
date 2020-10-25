<?php

namespace App\Containers\Geral\Jobs;

use App\Ship\Parents\Jobs\Job;
use App\Containers\Geral\Data\Repositories\Cadastro\EstadoRepository;
use Illuminate\Support\Facades\DB;
use Exception;

class SyncEstadosJob extends Job
{
    public $tries = 5;

    private $data;

    public function __construct($data = null)
    {
        $this->data = $data;
    }

    public function handle()
    {
        $client = new \GuzzleHttp\Client([
            'timeout'         => 30,
        ]);

        $response = $client->request('GET', 'https://servicodados.ibge.gov.br/api/v1/localidades/estados');
        if ($response) {
            $statusCode = $response->getStatusCode();
            $header = $response->getHeader('content-type')[0];

            if ($statusCode == 200 &&
                $header &&
                stripos($header, 'application/json') >= 0
            ) {
                $body = (string)$response->getBody();
                if ($body) {
                    $body = json_decode($body, true);
                    if ($body &&
                        is_array($body)
                    ) {
                        $repo = new EstadoRepository(app());
                        $estados = $repo->all();
                        if ($estados) {
                            foreach ($estados as $estado) {
                                if ($estado->sigla) {
                                    $found = false;
                                    foreach ($body as $row) {
                                        if ($row &&
                                            is_array($row) &&
                                            isset($row['sigla']) &&
                                            $row['sigla'] == $estado->sigla
                                        ) {
                                            $found = true;
                                            break;
                                        }
                                    }
                                    if (! $found) {
                                        DB::beginTransaction();
                                        try {
                                            if ($estado->delete()) {
                                                DB::commit();
                                            } else {
                                                DB::rollBack();
                                            }
                                        } catch (Exception $err) {
                                            DB::rollBack();
                                        }
                                    }
                                }
                            }
                        }
                        foreach ($body as $row) {
                            if ($row &&
                                is_array($row) &&
                                isset($row['id']) &&
                                isset($row['sigla']) &&
                                isset($row['nome']) &&
                                $row['id'] &&
                                $row['sigla'] &&
                                $row['nome']
                            ) {
                                $estado = $repo->findWhere([
                                    'sigla' => $row['sigla']
                                ])->first();
                                if (! $estado ||
                                    $estado->trashed()
                                ) {
                                    DB::beginTransaction();
                                    try {
                                        $estado = $repo->create([
                                            'nome' => $row['nome'],
                                            'sigla' => $row['sigla'],
                                            'id_ibge' => $row['id'],
                                        ]);
                                        if ($estado &&
                                            $estado->isValid() &&
                                            $estado->id
                                        ) {
                                            DB::commit();
                                        } else {
                                            DB::rollBack();
                                        }
                                    } catch (Exception $err) {
                                        DB::rollBack();
                                    }
                                } elseif ($estado->id_ibge != $row['id'] ||
                                    $estado->sigla != $row['sigla'] ||
                                    $estado->nome != $row['nome']
                                ) {
                                    DB::beginTransaction();
                                    try {
                                        $estado->fill([
                                            'nome' => $row['nome'],
                                            'sigla' => $row['sigla'],
                                            'id_ibge' => $row['id']
                                        ]);
                                        if ($estado->saveOrFail() &&
                                            $estado->isValid()
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
                        }
                        $estados = $repo->all();
                        if ($estados) {
                            foreach ($estados as $estado) {
                                if ($estado->id_ibge) {
                                    $found = false;
                                    try {
                                        //future json database column type for direct query?
                                        $queue = DB::table(config('queue.connections.database.table'))->orderBy('id')->get();
                                        foreach ($queue as $job) {
                                            try {
                                                if ($job &&
                                                    $job->payload
                                                ) {
                                                    $payload = json_decode($job->payload, true);
                                                    if ($payload &&
                                                        isset($payload['displayName']) &&
                                                        isset($payload['data']) &&
                                                        $payload['data'] &&
                                                        isset($payload['data']['command']) &&
                                                        $payload['data']['command'] &&
                                                        $payload['displayName'] == SyncEstadoCidadesJob::class
                                                    ) {
                                                        $command = unserialize($payload['data']['command']);
                                                        $commandArray = (array)$command;
                                                        if (! $commandArray ||
                                                            ! isset($commandArray["\x00".SyncEstadoCidadesJob::class."\x00idEstado"]) ||
                                                            ! $commandArray["\x00".SyncEstadoCidadesJob::class."\x00idEstado"] ||
                                                            $commandArray["\x00".SyncEstadoCidadesJob::class."\x00idEstado"] == $estado->id
                                                        ) {
                                                            $found = true;
                                                            break;
                                                        }
                                                    }
                                                }
                                            } catch (Exception $err) {
                                                $found = true;
                                            }
                                        }
                                        if (! $found) {
                                            dispatch(new SyncEstadoCidadesJob($estado->id));
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
