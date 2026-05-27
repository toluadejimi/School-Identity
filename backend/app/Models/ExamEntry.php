<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamEntry extends Model
{
    protected $fillable = [
        'exam_id',
        'student_id',
        'nfc_card_id',
        'device_id',
        'checked_by',
        'allowed',
        'denial_reason',
        'checked_at',
    ];

    protected function casts(): array
    {
        return [
            'allowed' => 'boolean',
            'checked_at' => 'datetime',
        ];
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
