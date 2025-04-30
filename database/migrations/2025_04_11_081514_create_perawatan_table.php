<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerawatanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('perawatan')) {
            Schema::create('perawatan', function (Blueprint $table) {
                $table->id();
                $table->string('no_perawatan')->nullable();
                $table->foreignId('users_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('no_seri_id')->constrained('no_seri')->onDelete('cascade');
                $table->enum('status', ['Belum Dilakukan Perawatan', 'Dalam Proses Perawatan', 'Selesai Perawatan'])->default('Belum Dilakukan Perawatan');
                $table->text('detail_perawatan')->nullable();
                $table->date('tgl_perawatan')->nullable();
                $table->date('tgl_mulai_perawatan')->nullable();
                $table->date('tgl_selesai_perawatan')->nullable();
                $table->time('waktu_perawatan')->nullable();
                $table->time('waktu_mulai')->nullable();
                $table->time('waktu_selesai')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('perawatan');
    }
}
