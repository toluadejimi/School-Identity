<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusRoute;
use App\Services\NfcResolverService;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusFareController extends Controller
{
    public function __construct(
        protected NfcResolverService $nfcResolver,
        protected WalletService $walletService,
    ) {}

    public function routes(): JsonResponse
    {
        $routes = BusRoute::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'fare_amount']);

        return response()->json(['routes' => $routes]);
    }

    public function pay(Request $request): JsonResponse
    {
        $data = $request->validate([
            'uid' => ['required', 'string'],
            'bus_route_id' => ['required', 'exists:bus_routes,id'],
        ]);

        $route = BusRoute::findOrFail($data['bus_route_id']);

        if ($route->status !== 'active') {
            return response()->json(['message' => 'Bus route is not active.'], 422);
        }

        $resolved = $this->nfcResolver->resolve(
            $data['uid'],
            $request->user(),
            $request->attributes->get('device'),
        );

        $result = $this->walletService->deductBusFare(
            $resolved['student'],
            $resolved['card'],
            $route,
            $request->user(),
            $resolved['device'],
        );

        return response()->json([
            'success' => true,
            'message' => 'Bus fare deducted successfully.',
            'reference' => $result['fare_transaction']->reference,
            'amount' => $result['fare_transaction']->amount,
            'balance' => $result['balance'],
            'student' => $this->nfcResolver->studentPayload($resolved['student']->fresh('wallet')),
            'route' => $route,
        ]);
    }
}
