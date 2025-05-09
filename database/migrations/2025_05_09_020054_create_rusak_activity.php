<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRusakActivity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rusak_activity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rusak_id')->constrained('rusak')->onDelete('cascade');
            $table->time('waktu_mulai')->nullable();
            $table->time('waktu_selesai')->nullable();
            $table->string('pic')->nullable();
            $table->text('detail_kerusakan')->nullable();
            $table->string('kondisi')->nullable();
            $table->string('status')->nullable();
            $table->text('alasan_penolakan')->nullable();
            $table->text('catatan')->nullable();
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
        Schema::dropIfExists('rusak_activity');
    }
}
