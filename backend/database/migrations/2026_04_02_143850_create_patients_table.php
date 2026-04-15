<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('card_id', 50)->nullable()->index();
            $table->string('qr_code', 100)->nullable()->index();
            $table->string('idcard', 50)->nullable()->index();
            $table->string('phone', 30)->nullable()->index();

            $table->string('name', 50)->nullable();
            $table->tinyInteger('gender')->nullable(); // -1 unknown, 0 male, 1 female
            $table->integer('age')->nullable();
            $table->string('birth', 10)->nullable(); // yyyyMMdd
            $table->integer('nation')->nullable();

            $table->longText('face_code')->nullable();
            $table->longText('face_photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
