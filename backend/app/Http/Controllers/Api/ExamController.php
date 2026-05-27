<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamEligibility;
use App\Models\ExamEntry;
use App\Services\NfcResolverService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function __construct(
        protected NfcResolverService $nfcResolver,
    ) {}

    public function index(): JsonResponse
    {
        $exams = Exam::query()
            ->whereIn('status', ['scheduled', 'ongoing'])
            ->orderBy('exam_date')
            ->get(['id', 'name', 'subject', 'exam_date', 'venue', 'status']);

        return response()->json(['exams' => $exams]);
    }

    public function scan(Request $request): JsonResponse
    {
        $data = $request->validate([
            'uid' => ['required', 'string'],
            'exam_id' => ['required', 'exists:exams,id'],
        ]);

        $exam = Exam::findOrFail($data['exam_id']);

        $resolved = $this->nfcResolver->resolve(
            $data['uid'],
            $request->user(),
            $request->attributes->get('device'),
        );

        $eligibility = ExamEligibility::where('exam_id', $exam->id)
            ->where('student_id', $resolved['student']->id)
            ->first();

        $allowed = $eligibility?->eligible ?? false;
        $reason = $allowed ? null : ($eligibility?->reason ?? 'Student is not eligible for this exam.');

        $entry = ExamEntry::create([
            'exam_id' => $exam->id,
            'student_id' => $resolved['student']->id,
            'nfc_card_id' => $resolved['card']->id,
            'device_id' => $resolved['device']->id,
            'checked_by' => $request->user()->id,
            'allowed' => $allowed,
            'denial_reason' => $reason,
            'checked_at' => now(),
        ]);

        $this->nfcResolver->recordTap(
            $resolved['uid'],
            $resolved['card'],
            $resolved['student'],
            $request->user(),
            $resolved['device'],
            'exam',
            $allowed ? 'allowed' : 'denied',
            ['entry_id' => $entry->id],
        );

        return response()->json([
            'success' => true,
            'allowed' => $allowed,
            'denial_reason' => $reason,
            'entry' => $entry,
            'student' => $this->nfcResolver->studentPayload($resolved['student']),
            'exam' => $exam,
        ]);
    }
}
