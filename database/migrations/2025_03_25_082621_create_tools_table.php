<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateToolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_id')->constrained('jenis')->onDelete('cascade');
            $table->string('kode')->nullable();
            $table->string('nama');
            $table->integer('stok_awal');
            $table->integer('stok_akhir')->nullable();
            $table->string('unit')->nullable();
            $table->integer('harga_total')->nullable();
            $table->string('pembelian')->nullable();
            $table->string('sumber')->nullable();
            $table->string('vendor')->nullable();
            $table->text('fungsi')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('gambar')->nullable();
            $table->integer('jadwal_perawatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tools');
    }
}
