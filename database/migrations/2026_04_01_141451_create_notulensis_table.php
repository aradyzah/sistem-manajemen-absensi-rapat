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
        Schema::create('notulensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rapat_id')->constrained('rapats')->onDelete('cascade');
            $table->string('pimpinan_rapat')->nullable();
            $table->string('sekretaris')->nullable(); // Notulis
            $table->longText('isi_notulensi')->nullable();
            $table->longText('daftar_keputusan')->nullable();
            $table->longText('tindak_lanjut')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notulensis');
    }
};
