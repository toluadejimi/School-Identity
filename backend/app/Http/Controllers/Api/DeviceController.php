<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'device_uuid' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'in:mobile,pos'],
        ]);

        $device = Device::updateOrCreate(
            ['device_uuid' => $data['device_uuid']],
            [
                'name' => $data['name'],
                'type' => $data['type'] ?? 'mobile',
                'status' => 'active',
                'registered_by' => $request->user()->id,
                'last_seen_at' => now(),
            ],
        );

        $request->user()->devices()->syncWithoutDetaching([$device->id]);

        return response()->json([
            'message' => 'Device registered successfully.',
            'device' => [
                'id' => $device->id,
                'name' => $device->name,
                'device_uuid' => $device->device_uuid,
                'type' => $device->type,
            ],
        ]);
    }
}
