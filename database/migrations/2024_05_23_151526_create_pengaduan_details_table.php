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
       Schema::create('Pengaduan_details', function (Blueprint $table) {
          $table->id();
          $table->unsignedBigInteger('user_id');
          $table->unsignedBigInteger('pengaduan_id');
          $table->text('content');
          $table->string('image');
          $table->timestamps();
    
          //relationship user
          $table->foreign('user_id')->references('id')->on('users');

           //relationship pengaduan
           $table->foreign('pengaduan_id')->references('id')->on('pengaduans');
       });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaduan_details');
    }
};
