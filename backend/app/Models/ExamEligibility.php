<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamEligibility extends Model
{
    protected $fillable = [
        'exam_id',
        'student_id',
        'eligible',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'eligible' => 'boolean',
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
