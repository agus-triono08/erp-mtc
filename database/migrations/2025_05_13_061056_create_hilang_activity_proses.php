<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHilangActivityProses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hilang_activity_proses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hilang_id')->constrained('hilang')->onDelete('cascade');
            $table->string('no_seri_old')->nullable();
            $table->string('no_seri_new')->nullable();
            $table->date('tgl_penggantian')->nullable();
            $table->string('harga')->nullable();
            $table->string('status')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('changed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hilang_activity_proses');
    }
}
