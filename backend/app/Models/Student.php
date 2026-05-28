<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'guardian_name',
        'guardian_phone',
        'class_name',
        'department',
        'session',
        'faculty',
        'level',
        'photo_path',
        'date_of_birth',
        'gender',
        'blood_group',
        'allergies',
        'medical_notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? url('api/v1/storage/'.$this->photo_path) : null;
    }

    public function nfcCards(): HasMany
    {
        return $this->hasMany(NfcCard::class);
    }

    public function activeCard(): HasOne
    {
        return $this->hasOne(NfcCard::class)->where('status', 'active')->latestOfMany();
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function clinicVisits(): HasMany
    {
        return $this->hasMany(ClinicVisit::class);
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function examEligibilities(): HasMany
    {
        return $this->hasMany(ExamEligibility::class);
    }
}
