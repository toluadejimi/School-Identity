<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicVisit extends Model
{
    protected $fillable = [
        'student_id',
        'nfc_card_id',
        'device_id',
        'staff_id',
        'visit_type',
        'symptoms',
        'treatment',
        'status',
        'checked_in_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'checked_in_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
