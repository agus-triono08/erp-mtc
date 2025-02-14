<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTechIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tech_issues', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('nama');
            $table->string('jabatan');
            $table->string('bagian');
            $table->string('jenis_sistem');
            $table->string('jenis_permintaan');
            $table->text('keterangan_permasalahan');
            $table->string('lampiran')->nullable();
            $table->timestamp('waktu_kebutuhan')->nullable();
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
        Schema::dropIfExists('tech_issues');
    }
}
