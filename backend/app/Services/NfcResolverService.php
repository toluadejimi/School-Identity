<?php

namespace App\Services;

use App\Models\CardTap;
use App\Models\Device;
use App\Models\NfcCard;
use App\Models\Student;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class NfcResolverService
{
    public function __construct(
        protected AuditService $auditService,
    ) {}

    public function resolve(string $rawUid, User $user, Device $device): array
    {
        $uid = NfcCard::normalizeUid($rawUid);

        if ($uid === '') {
            throw ValidationException::withMessages(['uid' => 'Invalid NFC card UID.']);
        }

        $device->update(['last_seen_at' => now()]);

        if (! $device->isActive()) {
            throw ValidationException::withMessages(['device' => 'This device is not active.']);
        }

        if (! $user->devices()->where('devices.id', $device->id)->exists() && ! $user->hasRole('admin')) {
            throw ValidationException::withMessages(['device' => 'You are not authorized to use this device.']);
        }

        $card = NfcCard::with('student.wallet')->where('uid', $uid)->first();

        if (! $card) {
            $this->recordTap($uid, null, null, $user, $device, 'unknown', 'card_not_found');

            throw ValidationException::withMessages(['uid' => 'NFC card is not registered.']);
        }

        if (! $card->isActive()) {
            $this->recordTap($uid, $card, $card->student, $user, $device, 'identity', 'card_inactive');

            throw ValidationException::withMessages(['uid' => 'NFC card is not active.']);
        }

        $student = $card->student;

        if (! $student || $student->status !== 'active') {
            $this->recordTap($uid, $card, $student, $user, $device, 'identity', 'student_inactive');

            throw ValidationException::withMessages(['student' => 'Student account is not active.']);
        }

        return compact('uid', 'card', 'student', 'device');
    }

    public function recordTap(
        string $uid,
        ?NfcCard $card,
        ?Student $student,
        User $user,
        Device $device,
        string $module,
        string $result,
        array $payload = [],
        ?\DateTimeInterface $tappedAt = null,
    ): CardTap {
        return CardTap::create([
            'uid' => $uid,
            'nfc_card_id' => $card?->id,
            'student_id' => $student?->id,
            'device_id' => $device->id,
            'user_id' => $user->id,
            'module' => $module,
            'result' => $result,
            'payload' => $payload,
            'tapped_at' => $tappedAt ?? now(),
        ]);
    }

    public function studentPayload(Student $student): array
    {
        return [
            'id' => $student->id,
            'student_number' => $student->student_number,
            'full_name' => $student->full_name,
            'email' => $student->email,
            'phone' => $student->phone,
            'address' => $student->address,
            'guardian_name' => $student->guardian_name,
            'guardian_phone' => $student->guardian_phone,
            'class_name' => $student->class_name,
            'department' => $student->department,
            'session' => $student->session,
            'faculty' => $student->faculty,
            'level' => $student->level,
            'photo_url' => $student->photo_url,
            'blood_group' => $student->blood_group,
            'allergies' => $student->allergies,
            'medical_notes' => $student->medical_notes,
            'status' => $student->status,
            'wallet_balance' => $student->wallet?->balance ?? '0.00',
            'wallet_currency' => $student->wallet?->currency ?? 'NGN',
        ];
    }
}
