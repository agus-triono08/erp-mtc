<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermintaanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permintaan', function (Blueprint $table) {
            $table->id();
            $table->string('no_permintaan')->nullable();
            $table->foreignId('users_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('tools_id')->nullable()->constrained()->onDelete('cascade');
            $table->date('tgl_permintaan')->nullable();
            $table->text('alasan_penolakan')->nullable();
            $table->integer('total')->nullable();
            $table->text('detail_permintaan')->nullable();
            $table->enum('status', ['Belum Diproses', 'Menunggu Diambil', 'Digunakan', 'Ditolak'])->default('Belum Diproses');
            $table->enum('status_kondisi', ['Belum Diproses', 'Menunggu Diambil', 'Digunakan', 'Ditolak'])->nullable()->default(null);
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
        Schema::dropIfExists('permintaan');
    }
}
