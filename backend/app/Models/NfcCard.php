<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NfcCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid',
        'student_id',
        'status',
        'issued_at',
        'deactivated_at',
        'replaced_by_card_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'deactivated_at' => 'datetime',
        ];
    }

    public static function normalizeUid(string $uid): string
    {
        return strtoupper(preg_replace('/[^0-9A-Fa-f]/', '', $uid) ?? $uid);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function replacedBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'replaced_by_card_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->student_id !== null;
    }
}
