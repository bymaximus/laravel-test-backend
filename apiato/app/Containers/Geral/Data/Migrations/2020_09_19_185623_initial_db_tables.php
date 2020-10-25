<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class InitialDbTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        ini_set('memory_limit', '-1');
        DB::unprepared(file_get_contents(database_path('migrations/initialdbtables.sql.temp')));
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
    }
}
