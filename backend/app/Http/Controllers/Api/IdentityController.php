<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NfcResolverService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IdentityController extends Controller
{
    public function __construct(
        protected NfcResolverService $nfcResolver,
    ) {}

    public function scan(Request $request): JsonResponse
    {
        $request->validate(['uid' => ['required', 'string']]);

        $resolved = $this->nfcResolver->resolve(
            $request->string('uid')->toString(),
            $request->user(),
            $request->attributes->get('device'),
        );

        $this->nfcResolver->recordTap(
            $resolved['uid'],
            $resolved['card'],
            $resolved['student'],
            $request->user(),
            $resolved['device'],
            'identity',
            'success',
        );

        return response()->json([
            'success' => true,
            'student' => $this->nfcResolver->studentPayload($resolved['student']),
            'card' => [
                'uid' => $resolved['card']->uid,
                'status' => $resolved['card']->status,
            ],
        ]);
    }
}
