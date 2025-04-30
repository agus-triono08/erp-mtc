<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoSeriTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('no_seri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('layout_id')->constrained()->onDelete('cascade');
            $table->foreignId('tools_id')->constrained()->onDelete('cascade');
            $table->string('no_seri')->nullable();
            $table->string('no_seri_default')->nullable();
            $table->integer('harga')->nullable();
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_kondisi')->nullable();
            $table->enum('kondisi', ['OK', 'Error', 'Rusak', 'Musnah', 'Hilang'])->default('OK');
            $table->string('kondisi_after')->nullable();
            $table->string('reject_reason')->nullable();
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
        Schema::dropIfExists('no_seri');
    }
}
