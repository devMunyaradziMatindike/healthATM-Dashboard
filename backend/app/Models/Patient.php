<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $fillable = [
        'card_id',
        'qr_code',
        'idcard',
        'phone',
        'name',
        'gender',
        'age',
        'birth',
        'nation',
        'face_code',
        'face_photo',
    ];

    public function measurements(): HasMany
    {
        return $this->hasMany(Measurement::class);
    }
}
