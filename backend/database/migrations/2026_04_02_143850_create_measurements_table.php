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
        Schema::create('measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->cascadeOnDelete();
            $table->unsignedBigInteger('patient_id')->nullable()->index();

            $table->string('token', 50)->nullable();
            $table->string('equip_id', 50)->index();
            $table->integer('serial_number')->nullable();
            $table->string('equip_model', 20)->nullable();
            $table->string('equip_number', 20)->nullable();

            $table->string('card_id', 50)->nullable()->index();
            $table->integer('card_type')->nullable();
            $table->string('name', 50)->nullable();
            $table->tinyInteger('gender')->nullable();
            $table->integer('age')->nullable();
            $table->string('birth', 10)->nullable();
            $table->integer('nation')->nullable();
            $table->integer('coin')->nullable();
            $table->string('qr_code', 100)->nullable()->index();
            $table->string('cell_phone', 30)->nullable();

            // vitals/body composition (integer fields are often scaled x10 per device spec)
            $table->integer('height')->nullable(); // x10
            $table->integer('weight')->nullable(); // x10
            $table->integer('body_temperature')->nullable(); // x10
            $table->integer('systolic_bp')->nullable();
            $table->integer('diastolic_bp')->nullable();
            $table->integer('pulse_per_minute')->nullable();
            $table->integer('blood_oxygen_saturation')->nullable();

            $table->integer('fat_rate')->nullable(); // x10
            $table->integer('fat_mass')->nullable(); // x10
            $table->integer('basal_metabolism')->nullable();
            $table->integer('body_moisture_rate')->nullable(); // x10
            $table->integer('body_moisture_rate_core')->nullable();
            $table->integer('skeletal_muscle')->nullable(); // x10
            $table->integer('skeletal_muscle_score')->nullable();
            $table->integer('visceral_fat_index')->nullable(); // x10
            $table->integer('visceral_fat_index_core')->nullable();
            $table->integer('bone_mineral_content')->nullable(); // x10
            $table->integer('bone_mineral_content_score')->nullable();
            $table->integer('extracellular_fluid')->nullable(); // x10
            $table->integer('intracellular_fluid')->nullable(); // x10
            $table->integer('moisture')->nullable(); // x10
            $table->integer('protein')->nullable(); // x10
            $table->integer('inorganic_salts')->nullable(); // x10
            $table->integer('physical_age')->nullable();
            $table->integer('overall_rating')->nullable(); // aka comprehensive score / points

            $table->integer('blood_sugar')->nullable();
            $table->integer('alcohol')->nullable();
            $table->string('alcohol_result', 20)->nullable();

            $table->longText('face_code')->nullable();
            $table->longText('face_photo')->nullable();

            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->integer('utc')->nullable();
            $table->date('measure_date')->nullable();

            $table->string('tag1', 100)->nullable();
            $table->string('tag2', 100)->nullable();
            $table->string('tag3', 100)->nullable();
            $table->string('tag4', 100)->nullable();
            $table->string('tag5', 100)->nullable();
            $table->string('tag6', 100)->nullable();
            $table->string('tag7', 100)->nullable();
            $table->string('tag8', 100)->nullable();
            $table->string('tag9', 100)->nullable();
            $table->string('tag10', 100)->nullable();

            $table->json('raw_payload')->nullable();
            $table->timestamps();

            $table->unique(['equip_id', 'measure_date', 'serial_number'], 'measurements_device_day_serial_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('measurements');
    }
};
