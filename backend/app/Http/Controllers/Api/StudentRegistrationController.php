<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NfcCard;
use App\Models\Student;
use App\Services\AuditService;
use App\Services\NfcResolverService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StudentRegistrationController extends Controller
{
    public function __construct(
        protected AuditService $auditService,
        protected NfcResolverService $nfcResolver,
    ) {}

    public function store(Request $request): JsonResponse
    {
        if (! $request->user()->hasAnyRole(['admin', 'attendance', 'clinic'])) {
            abort(403, 'You are not allowed to register students.');
        }

        $data = $request->validate([
            'uid' => ['required', 'string', 'max:64'],
            'student_number' => ['required', 'string', 'max:100', Rule::unique('students', 'student_number')],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'guardian_phone' => ['nullable', 'string', 'max:50'],
            'class_name' => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:100'],
            'session' => ['nullable', 'string', 'max:50'],
            'faculty' => ['nullable', 'string', 'max:150'],
            'level' => ['nullable', 'string', 'max:50'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'max:20'],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'allergies' => ['nullable', 'string'],
            'medical_notes' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:4096'],
        ]);

        $uid = NfcCard::normalizeUid($data['uid']);
        if ($uid === '') {
            throw ValidationException::withMessages(['uid' => 'Invalid NFC card UID.']);
        }

        $existingCard = NfcCard::where('uid', $uid)->first();
        if ($existingCard && $existingCard->student_id !== null) {
            throw ValidationException::withMessages(['uid' => 'This NFC card is already mapped to a student.']);
        }

        $device = $request->attributes->get('device');

        $student = DB::transaction(function () use ($request, $data, $uid, $existingCard, $device) {
            if ($request->hasFile('photo')) {
                $data['photo_path'] = $request->file('photo')->store('students', 'public');
            }

            unset($data['uid'], $data['photo']);
            $data['status'] = 'active';

            $student = Student::create($data);

            $card = $existingCard ?: new NfcCard(['uid' => $uid]);
            $card->fill([
                'student_id' => $student->id,
                'status' => 'active',
                'issued_at' => now(),
                'deactivated_at' => null,
                'notes' => 'Registered from mobile app',
            ]);
            $card->save();

            $student->load(['wallet', 'activeCard']);

            $this->nfcResolver->recordTap(
                $uid,
                $card,
                $student,
                $request->user(),
                $device,
                'registration',
                'success',
            );

            $this->auditService->log(
                'student.registered_from_mobile',
                Student::class,
                $student->id,
                ['uid' => $uid],
                $request->user()->id,
                $device?->id,
                $request,
            );

            return $student;
        });

        return response()->json([
            'success' => true,
            'message' => 'Student registered and NFC card mapped successfully.',
            'student' => $this->nfcResolver->studentPayload($student),
            'card' => [
                'uid' => $student->activeCard?->uid,
                'status' => $student->activeCard?->status,
            ],
        ], 201);
    }
}
