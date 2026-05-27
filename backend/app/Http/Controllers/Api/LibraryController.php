<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CardTap;
use App\Services\NfcResolverService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LibraryController extends Controller
{
    public function __construct(
        protected NfcResolverService $nfcResolver,
    ) {}

    public function checkIn(Request $request): JsonResponse
    {
        return $this->recordLibraryPass($request, 'library-check-in', 'Library check-in recorded');
    }

    public function checkOut(Request $request): JsonResponse
    {
        return $this->recordLibraryPass($request, 'library-check-out', 'Library check-out recorded');
    }

    public function history(Request $request): JsonResponse
    {
        $history = CardTap::with('student')
            ->whereIn('module', ['library-check-in', 'library-check-out'])
            ->latest('tapped_at')
            ->limit(50)
            ->get()
            ->map(fn (CardTap $tap): array => [
                'id' => $tap->id,
                'module' => $tap->module,
                'result' => $tap->result,
                'uid' => $tap->uid,
                'student_name' => $tap->student?->full_name,
                'student_number' => $tap->student?->student_number,
                'tapped_at' => $tap->tapped_at?->toIso8601String(),
            ]);

        return response()->json([
            'success' => true,
            'history' => $history,
        ]);
    }

    protected function recordLibraryPass(Request $request, string $module, string $message): JsonResponse
    {
        $data = $request->validate([
            'uid' => ['required', 'string'],
            'offline_recorded_at' => ['nullable', 'date'],
        ]);
        $recordedAt = isset($data['offline_recorded_at']) ? Carbon::parse($data['offline_recorded_at']) : now();

        $resolved = $this->nfcResolver->resolve(
            $data['uid'],
            $request->user(),
            $request->attributes->get('device'),
        );

        $tap = $this->nfcResolver->recordTap(
            $resolved['uid'],
            $resolved['card'],
            $resolved['student'],
            $request->user(),
            $resolved['device'],
            $module,
            'success',
            ['offline_recorded_at' => $data['offline_recorded_at'] ?? null],
            $recordedAt,
        );

        return response()->json([
            'success' => true,
            'message' => $message,
            'tap_id' => $tap->id,
            'student' => $this->nfcResolver->studentPayload($resolved['student']),
        ], 201);
    }
}
