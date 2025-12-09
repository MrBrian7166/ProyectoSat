<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('resultados_sat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained()->onDelete('cascade');
            $table->string('periodo', 7); // Formato: YYYY-MM
            $table->enum('resultado', ['POSITIVO', 'NEGATIVO', 'ERROR', 'PENDIENTE']);
            $table->text('detalles')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('resultados_sat');
    }
};