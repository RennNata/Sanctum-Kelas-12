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
        Schema::create('films', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->date('tanggal_rilis');
            $table->foreignId('id_genre')->constrained('genres')->cascadeOnDelete();
            $table->text('deskripsi');
            $table->integer('durasi');
            $table->float('rating', 2, 1);
            $table->string('sutradara');
            $table->string('poster')->nullable();
            $table->timestamps();
        });

        Schema::create('aktor_film', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_film')->constrained('films')->cascadeOnDelete();
            $table->foreignId('id_aktor')->constrained('aktors')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
        Schema::dropIfExists('aktor_film');
        Schema::dropIfExists('films');

    }
};
