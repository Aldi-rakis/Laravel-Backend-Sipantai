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
        Schema::create('beritas', function (Blueprint $table) {
            $table->id();   
            $table->string('title');
            $table->string('description');
            $table->string('image');
            $table->date('waktu_upload')->nullable(); // Menambahkan kolom waktu_upload
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beritas');
    }


    // Event listener untuk menyetel waktu_upload saat model dibuat
    protected static function boot()
    {
        Schema::boot();

        Schema::creating(function ($berita) {
            $berita->waktu_upload = $berita->created_at->format('Y-m-d');
        });
    }
};
