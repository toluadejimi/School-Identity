<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardTap extends Model
{
    protected $fillable = [
        'uid',
        'nfc_card_id',
        'student_id',
        'device_id',
        'user_id',
        'module',
        'result',
        'payload',
        'tapped_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'tapped_at' => 'datetime',
        ];
    }

    public function nfcCard(): BelongsTo
    {
        return $this->belongsTo(NfcCard::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
