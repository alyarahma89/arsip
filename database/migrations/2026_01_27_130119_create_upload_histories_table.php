<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('upload_histories', function (Blueprint $table) {
        $table->id();
        $table->string('file_name');
        $table->string('type'); // 'DTS' atau 'Lapgas'
        $table->string('status'); // 'Sukses' atau 'Gagal'
        $table->string('message')->nullable(); // Pesan error/sukses
        $table->string('user_name'); // Siapa yang upload
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
        Schema::dropIfExists('upload_histories');
    }
};
