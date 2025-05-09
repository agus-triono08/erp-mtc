<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMusnahActivity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('musnah_activity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('musnah_id')->constrained('musnah')->onDelete('cascade');
            $table->string('dokumen_pemusnahan')->nullable();
            $table->string('berita_acara')->nullable();
            $table->text('detail_pemusnahan')->nullable();
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
        Schema::dropIfExists('musnah_activity');
    }
}
