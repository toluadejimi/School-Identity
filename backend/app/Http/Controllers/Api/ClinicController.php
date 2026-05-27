<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClinicVisit;
use App\Services\NfcResolverService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ClinicController extends Controller
{
    public function __construct(
        protected NfcResolverService $nfcResolver,
    ) {}

    public function checkIn(Request $request): JsonResponse
    {
        $data = $request->validate([
            'uid' => ['required', 'string'],
            'symptoms' => ['nullable', 'string'],
            'visit_type' => ['nullable', 'string', 'max:50'],
            'offline_recorded_at' => ['nullable', 'date'],
        ]);
        $checkedInAt = isset($data['offline_recorded_at']) ? Carbon::parse($data['offline_recorded_at']) : now();

        $resolved = $this->nfcResolver->resolve(
            $data['uid'],
            $request->user(),
            $request->attributes->get('device'),
        );

        $visit = ClinicVisit::create([
            'student_id' => $resolved['student']->id,
            'nfc_card_id' => $resolved['card']->id,
            'device_id' => $resolved['device']->id,
            'staff_id' => $request->user()->id,
            'visit_type' => $data['visit_type'] ?? 'check_in',
            'symptoms' => $data['symptoms'] ?? null,
            'status' => 'checked_in',
            'checked_in_at' => $checkedInAt,
        ]);

        $this->nfcResolver->recordTap(
            $resolved['uid'],
            $resolved['card'],
            $resolved['student'],
            $request->user(),
            $resolved['device'],
            'clinic',
            'success',
            ['visit_id' => $visit->id, 'offline_recorded_at' => $data['offline_recorded_at'] ?? null],
            $checkedInAt,
        );

        return response()->json([
            'success' => true,
            'visit' => $visit,
            'student' => $this->nfcResolver->studentPayload($resolved['student']),
        ], 201);
    }

    public function update(Request $request, ClinicVisit $visit): JsonResponse
    {
        $data = $request->validate([
            'treatment' => ['nullable', 'string'],
            'status' => ['nullable', 'in:checked_in,in_progress,completed'],
        ]);

        $visit->fill($data);

        if (($data['status'] ?? null) === 'completed') {
            $visit->completed_at = now();
        }

        $visit->save();

        return response()->json(['success' => true, 'visit' => $visit]);
    }
}
