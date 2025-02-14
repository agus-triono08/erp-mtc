<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRincianAlatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rincian_alats', function (Blueprint $table) {
            $table->id();
            $table->string('kode_alat'); // Foreign key ke tabel 'alats'
            $table->string('brand');
            $table->string('kode_rincian_alat')->unique();
            $table->integer('jumlah');
            $table->string('kondisi');
            $table->string('gambar')->nullable(); // Kolom untuk gambar, nullable jika opsional
            $table->timestamps(); // Keep this line only once

            // Foreign key constraint
            $table->foreign('kode_alat')->references('kode_alat')->on('alats')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rincian_alats');
    }
}
