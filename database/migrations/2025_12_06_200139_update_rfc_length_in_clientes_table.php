<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('clientes', function (Blueprint $table) {
            // Cambiar la longitud de RFC de 12 a 13 caracteres
            $table->string('rfc', 13)->change();
        });
    }

    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            // Revertir a 12 caracteres si es necesario
            $table->string('rfc', 12)->change();
        });
    }
};