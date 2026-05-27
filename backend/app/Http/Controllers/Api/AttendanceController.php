<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Services\NfcResolverService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class AttendanceController extends Controller
{
    public function __construct(
        protected NfcResolverService $nfcResolver,
    ) {}

    public function sessions(Request $request): JsonResponse
    {
        $sessions = AttendanceSession::query()
            ->where('status', 'open')
            ->orderByDesc('session_date')
            ->limit(50)
            ->get(['id', 'name', 'class_name', 'session_date', 'status']);

        return response()->json(['sessions' => $sessions]);
    }

    public function scan(Request $request): JsonResponse
    {
        $data = $request->validate([
            'uid' => ['required', 'string'],
            'attendance_session_id' => ['required', 'exists:attendance_sessions,id'],
            'offline_recorded_at' => ['nullable', 'date'],
        ]);
        $recordedAt = isset($data['offline_recorded_at']) ? Carbon::parse($data['offline_recorded_at']) : now();

        $session = AttendanceSession::findOrFail($data['attendance_session_id']);

        if ($session->status !== 'open') {
            throw ValidationException::withMessages(['attendance_session_id' => 'Attendance session is closed.']);
        }

        $resolved = $this->nfcResolver->resolve(
            $data['uid'],
            $request->user(),
            $request->attributes->get('device'),
        );

        $existing = AttendanceRecord::where('attendance_session_id', $session->id)
            ->where('student_id', $resolved['student']->id)
            ->exists();

        if ($existing) {
            $this->nfcResolver->recordTap(
                $resolved['uid'],
                $resolved['card'],
                $resolved['student'],
                $request->user(),
                $resolved['device'],
                'attendance',
                'duplicate',
            );

            throw ValidationException::withMessages(['uid' => 'Attendance already recorded for this student.']);
        }

        $record = AttendanceRecord::create([
            'attendance_session_id' => $session->id,
            'student_id' => $resolved['student']->id,
            'nfc_card_id' => $resolved['card']->id,
            'device_id' => $resolved['device']->id,
            'recorded_by' => $request->user()->id,
            'recorded_at' => $recordedAt,
        ]);

        $this->nfcResolver->recordTap(
            $resolved['uid'],
            $resolved['card'],
            $resolved['student'],
            $request->user(),
            $resolved['device'],
            'attendance',
            'success',
            ['record_id' => $record->id, 'offline_recorded_at' => $data['offline_recorded_at'] ?? null],
            $recordedAt,
        );

        return response()->json([
            'success' => true,
            'record' => $record,
            'student' => $this->nfcResolver->studentPayload($resolved['student']),
        ], 201);
    }
}
