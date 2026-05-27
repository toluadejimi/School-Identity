<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    protected $fillable = [
        'name',
        'subject',
        'exam_date',
        'start_time',
        'end_time',
        'venue',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'exam_date' => 'date',
        ];
    }

    public function eligibilities(): HasMany
    {
        return $this->hasMany(ExamEligibility::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(ExamEntry::class);
    }
}
