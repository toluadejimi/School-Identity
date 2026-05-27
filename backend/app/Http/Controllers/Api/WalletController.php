<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\JsonResponse;

class WalletController extends Controller
{
    public function show(Student $student): JsonResponse
    {
        $student->load('wallet');

        return response()->json([
            'student_id' => $student->id,
            'balance' => $student->wallet?->balance ?? '0.00',
            'currency' => $student->wallet?->currency ?? 'NGN',
            'status' => $student->wallet?->status ?? 'inactive',
        ]);
    }
}
