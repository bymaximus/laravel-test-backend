<?php

namespace App\Containers\Geral\Tasks\Cadastro\Estado;

use App\Containers\Geral\Data\Repositories\Cadastro\EstadoRepository;
use App\Containers\Geral\Jobs\SyncEstadosJob;
use App\Ship\Parents\Tasks\Task;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Bus\PendingDispatch;
use Exception;

class SincronizarTask extends Task
{
    protected $repository;

    public function __construct(EstadoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function run()
    {
        try {
            $found = false;
            $queue = DB::table(config('queue.connections.database.table'))->orderBy('id')->get();
            foreach ($queue as $job) {
                try {
                    if ($job &&
                        $job->payload
                    ) {
                        $payload = json_decode($job->payload, true);
                        if ($payload &&
                            isset($payload['displayName']) &&
                            $payload['displayName'] == SyncEstadosJob::class
                        ) {
                            $found = true;
                            break;
                        }
                    }
                } catch (Exception $err) {
                    $found = true;
                }
            }
            if (! $found) {
                $result = dispatch(new SyncEstadosJob());
                if ($result &&
                    $result instanceof PendingDispatch
                ) {
                    return true;
                }
                return false;
            }
            return null;
        } catch (Exception $err) {
            return false;
        }
    }
}
