<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('vendas', function (Blueprint $table) {
            $table->foreignId('user_id')
                  ->after('cliente_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('restrict');
        });

        if (DB::table('vendas')->count() > 0) {
            $defaultUserId = DB::table('users')->first()->id;

            DB::table('vendas')
                ->whereNull('user_id')
                ->update(['user_id' => $defaultUserId]);
        }

        Schema::table('vendas', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }

    public function down()
    {
        Schema::table('vendas', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
