<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Measurement extends Model
{
    protected $fillable = [
        'device_id',
        'patient_id',
        'token',
        'equip_id',
        'serial_number',
        'equip_model',
        'equip_number',
        'card_id',
        'card_type',
        'name',
        'gender',
        'age',
        'birth',
        'nation',
        'coin',
        'qr_code',
        'cell_phone',
        'height',
        'weight',
        'body_temperature',
        'systolic_bp',
        'diastolic_bp',
        'pulse_per_minute',
        'blood_oxygen_saturation',
        'fat_rate',
        'fat_mass',
        'basal_metabolism',
        'body_moisture_rate',
        'body_moisture_rate_core',
        'skeletal_muscle',
        'skeletal_muscle_score',
        'visceral_fat_index',
        'visceral_fat_index_core',
        'bone_mineral_content',
        'bone_mineral_content_score',
        'extracellular_fluid',
        'intracellular_fluid',
        'moisture',
        'protein',
        'inorganic_salts',
        'physical_age',
        'overall_rating',
        'blood_sugar',
        'alcohol',
        'alcohol_result',
        'face_code',
        'face_photo',
        'start_time',
        'end_time',
        'utc',
        'measure_date',
        'tag1',
        'tag2',
        'tag3',
        'tag4',
        'tag5',
        'tag6',
        'tag7',
        'tag8',
        'tag9',
        'tag10',
        'raw_payload',
    ];

    protected $casts = [
        'raw_payload' => 'array',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'measure_date' => 'date',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
