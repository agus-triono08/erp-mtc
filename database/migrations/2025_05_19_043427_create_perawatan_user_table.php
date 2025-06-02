<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerawatanUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perawatan_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perawatan_id')->constrained('perawatan')->onDelete('cascade');
            // $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');

            $table->foreign('user_id')
                ->references('id')
                ->on('erp_spa.users')
                ->onDelete('cascade');
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
        Schema::dropIfExists('perawatan_user');
    }
}
