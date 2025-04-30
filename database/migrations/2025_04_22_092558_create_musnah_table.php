<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMusnahTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('musnah', function (Blueprint $table) {
            $table->id();
            $table->string('no_pemusnahan')->nullable();
            $table->foreignId('users_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('no_seri_id')->nullable()->constrained('no_seri')->onDelete('cascade');            
            $table->text('detail_pemusnahan')->nullable();
            $table->date('tgl_pemusnahan');
            $table->string('dokumen_pemusnahan')->nullable();
            $table->string('berita_acara')->nullable();
            $table->enum('kondisi', ['OK', 'Error', 'Rusak', 'Musnah', 'Hilang'])->default('Musnah');
            $table->enum('status', ['Belum', 'Proses', 'Selesai'])->default('Belum');
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
        Schema::dropIfExists('musnah');
    }
}
