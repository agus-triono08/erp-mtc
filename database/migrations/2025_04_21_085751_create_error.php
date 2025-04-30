<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateError extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('error', function (Blueprint $table) {
            $table->id();
            $table->string('no_perbaikan')->nullable();
            $table->foreignId('users_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('no_seri_id')->nullable()->constrained('no_seri')->onDelete('cascade');            
            $table->date('tgl_perbaikan')->nullable();
            $table->text('detail_perbaikan')->nullable();
            $table->enum('kondisi', ['OK', 'Error', 'Rusak', 'Musnah', 'Hilang'])->default('Error');
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
        Schema::dropIfExists('error');
    }
}
