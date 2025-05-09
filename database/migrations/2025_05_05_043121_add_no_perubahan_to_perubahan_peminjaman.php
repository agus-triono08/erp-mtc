<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNoPerubahanToPerubahanPeminjaman extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('perubahan_peminjaman', function (Blueprint $table) {
            $table->string('no_perubahan')->nullable();
            $table->text('alasan_penolakan')->nullable();
            $table->enum('status', ['Belum Diproses', 'Disetujui', 'Ditolak'])->default('Belum Diproses');
            $table->date('tgl_kembali')->nullable();            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('perubahan_peminjaman', function (Blueprint $table) {
            $table->dropColumn(['no_perubahan', 'alasan_penolakan', 'status', 'tgl_kembali']);
        });
    }
}
