<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusPerubahanToNoSeri extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('no_seri', function (Blueprint $table) {
            $table->string('status_perubahan')->nullable();
            $table->text('alasan_penolakan_perubahan')->nullable();
            $table->date('tgl_perubahan')->nullable();
            $table->date('tgl_pengecekan')->nullable();
            $table->text('deskripsi_cek')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('no_seri', function (Blueprint $table) {
            $table->dropColumn(['status_perubahan', 'alasan_penolakan_perubahan', 'tgl_perubahan', 'tgl_pengecekan', 'deskripsi_cek']);
        });
    }
}
