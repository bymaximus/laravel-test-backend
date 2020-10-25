<?php

namespace App\Containers\Geral\Data\Seeders;

use App\Ship\Parents\Seeders\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use \Exception;

/**
 * Class GeralInitialDbSeeder
 *
 */
class GeralInitialDbSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (config('app.env') == 'testing') {
            ini_set('memory_limit', '-1');
            DB::unprepared(file_get_contents(database_path('seeds/initialdbtablesdata.sql.temp')));
        }
    }
}
