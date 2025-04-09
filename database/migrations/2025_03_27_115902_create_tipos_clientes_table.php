<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tipos_documentos', function (Blueprint $table) {
            $table->id();
            $table->string('documento', 30);
            $table->string('codigo', 10);
            $table->timestamps();
        });


        Schema::create('tipos_personas', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_persona', 30);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_documentos');
        Schema::dropIfExists('tipos_personas');
    }
};

