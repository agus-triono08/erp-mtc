<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoSeriLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('no_seri_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('no_seri_id')->constrained('no_seri')->onDelete('cascade');
            $table->string('old_kondisi')->nullable();
            $table->string('new_kondisi')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('changed_at')->nullable();
            $table->unsignedBigInteger('changed_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('no_seri_log');
    }
}
