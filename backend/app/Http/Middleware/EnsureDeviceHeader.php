<?php

namespace App\Http\Middleware;

use App\Models\Device;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDeviceHeader
{
    public function handle(Request $request, Closure $next): Response
    {
        $deviceUuid = $request->header('X-Device-UUID');

        if (! $deviceUuid) {
            return response()->json(['message' => 'X-Device-UUID header is required.'], 422);
        }

        $device = Device::where('device_uuid', $deviceUuid)->first();

        if (! $device) {
            return response()->json(['message' => 'Device is not registered.'], 403);
        }

        $request->attributes->set('device', $device);

        return $next($request);
    }
}
