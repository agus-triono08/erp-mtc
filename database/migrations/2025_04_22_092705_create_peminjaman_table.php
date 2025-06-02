<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeminjamanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->string('no_peminjaman')->nullable();
            // $table->foreignId('users_id')->nullable()->constrained()->onDelete('cascade');
            // Foreign key ke users di database erp_spa
            $table->unsignedBigInteger('users_id')->nullable();
            $table->foreign('users_id')
                  ->references('id')
                  ->on('erp_spa.users')
                  ->onDelete('cascade');
            $table->foreignId('tools_id')->nullable()->constrained()->onDelete('cascade');
            $table->date('tgl_pinjam')->nullable();
            $table->date('tgl_kembali')->nullable();
            $table->text('detail_peminjaman')->nullable();
            $table->text('alasan_penolakan')->nullable();
            $table->text('deskripsi_cek')->nullable();
            $table->date('tgl_cek')->nullable();
            $table->integer('total')->nullable();
            $table->enum('status', ['Belum Diproses', 'Menunggu Diambil', 'Dipinjam', 'Ditolak', 'Selesai'])->default('Belum Diproses');            
            $table->enum('status_kondisi', ['Belum Diproses', 'Menunggu Diambil', 'Dipinjam', 'Ditolak', 'Selesai'])->nullable()->default(null);
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
        Schema::dropIfExists('peminjaman');
    }
}
