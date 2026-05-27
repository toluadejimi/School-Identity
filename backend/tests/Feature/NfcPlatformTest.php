<?php

namespace Tests\Feature;

use App\Models\AttendanceSession;
use App\Models\BusRoute;
use App\Models\Device;
use App\Models\Exam;
use App\Models\ExamEligibility;
use App\Models\NfcCard;
use App\Models\Student;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NfcPlatformTest extends TestCase
{
    use RefreshDatabase;

    protected User $staff;

    protected Device $device;

    protected Student $student;

    protected NfcCard $card;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'transport', 'guard_name' => 'web']);
        Role::create(['name' => 'clinic', 'guard_name' => 'web']);

        $this->staff = User::factory()->create([
            'email' => 'staff@test.local',
            'password' => Hash::make('password'),
        ]);
        $this->staff->assignRole('transport');
        $this->staff->assignRole('clinic');

        $this->device = Device::create([
            'name' => 'Test Phone',
            'device_uuid' => 'TEST-DEVICE-UUID',
            'type' => 'mobile',
            'status' => 'active',
        ]);

        $this->staff->devices()->attach($this->device->id);

        $this->student = Student::factory()->create();
        $this->student->wallet->update(['balance' => 500]);
        $this->card = NfcCard::create([
            'uid' => '04A1B2C3D4',
            'student_id' => $this->student->id,
            'status' => 'active',
            'issued_at' => now(),
        ]);
    }

    protected function apiHeaders(string $token): array
    {
        return [
            'Authorization' => 'Bearer '.$token,
            'X-Device-UUID' => $this->device->device_uuid,
            'Accept' => 'application/json',
        ];
    }

    public function test_staff_can_login_and_scan_identity(): void
    {
        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'staff@test.local',
            'password' => 'password',
            'device_name' => 'test',
        ]);

        $login->assertOk()->assertJsonStructure(['token', 'user']);

        $token = $login->json('token');

        $this->postJson('/api/v1/devices/register', [
            'device_uuid' => $this->device->device_uuid,
            'name' => 'Test Phone',
        ], $this->apiHeaders($token))->assertOk();

        $scan = $this->postJson('/api/v1/identity/scan', [
            'uid' => '04:a1:b2:c3:d4',
        ], $this->apiHeaders($token));

        $scan->assertOk()
            ->assertJsonPath('student.student_number', $this->student->student_number);
    }

    public function test_staff_can_register_student_and_map_nfc_card(): void
    {
        Storage::fake('public');

        $token = $this->postJson('/api/v1/auth/login', [
            'email' => 'staff@test.local',
            'password' => 'password',
            'device_name' => 'test',
        ])->json('token');

        $response = $this->post('/api/v1/students/register', [
            'uid' => '04:11:22:33:44',
            'student_number' => 'MAT-2026-001',
            'first_name' => 'Grace',
            'last_name' => 'Amina',
            'session' => '2025/2026',
            'faculty' => 'Science',
            'department' => 'Computer Science',
            'level' => '200',
            'photo' => UploadedFile::fake()->image('student.jpg'),
        ], $this->apiHeaders($token));

        $response->assertCreated()
            ->assertJsonPath('student.student_number', 'MAT-2026-001')
            ->assertJsonPath('card.uid', '0411223344');

        $this->assertDatabaseHas('students', [
            'student_number' => 'MAT-2026-001',
            'faculty' => 'Science',
            'level' => '200',
        ]);

        $this->assertDatabaseHas('nfc_cards', [
            'uid' => '0411223344',
            'status' => 'active',
        ]);
    }

    public function test_bus_fare_deducts_wallet_balance(): void
    {
        $route = BusRoute::create([
            'name' => 'Route B',
            'code' => 'RB',
            'fare_amount' => 100,
            'status' => 'active',
        ]);

        $token = $this->postJson('/api/v1/auth/login', [
            'email' => 'staff@test.local',
            'password' => 'password',
            'device_name' => 'test',
        ])->json('token');

        $response = $this->postJson('/api/v1/bus-fare/scan', [
            'uid' => $this->card->uid,
            'bus_route_id' => $route->id,
        ], $this->apiHeaders($token));

        $response->assertOk()->assertJsonPath('balance', 400);

        $this->assertDatabaseHas('wallets', [
            'student_id' => $this->student->id,
            'balance' => 400,
        ]);
    }

    public function test_insufficient_wallet_balance_is_rejected(): void
    {
        $this->student->wallet->update(['balance' => 10]);

        $route = BusRoute::create([
            'name' => 'Route C',
            'code' => 'RC',
            'fare_amount' => 100,
            'status' => 'active',
        ]);

        $token = $this->postJson('/api/v1/auth/login', [
            'email' => 'staff@test.local',
            'password' => 'password',
            'device_name' => 'test',
        ])->json('token');

        $this->postJson('/api/v1/bus-fare/scan', [
            'uid' => $this->card->uid,
            'bus_route_id' => $route->id,
        ], $this->apiHeaders($token))->assertStatus(422);
    }

    public function test_exam_scan_respects_eligibility(): void
    {
        $exam = Exam::create([
            'name' => 'Physics',
            'exam_date' => now()->toDateString(),
            'status' => 'scheduled',
        ]);

        ExamEligibility::create([
            'exam_id' => $exam->id,
            'student_id' => $this->student->id,
            'eligible' => false,
            'reason' => 'Fees not cleared',
        ]);

        $token = $this->postJson('/api/v1/auth/login', [
            'email' => 'staff@test.local',
            'password' => 'password',
            'device_name' => 'test',
        ])->json('token');

        $response = $this->postJson('/api/v1/exams/scan', [
            'uid' => $this->card->uid,
            'exam_id' => $exam->id,
        ], $this->apiHeaders($token));

        $response->assertOk()
            ->assertJsonPath('allowed', false)
            ->assertJsonPath('denial_reason', 'Fees not cleared');
    }

    public function test_attendance_prevents_duplicate_scan(): void
    {
        $session = AttendanceSession::create([
            'name' => 'Class Roll',
            'session_date' => now()->toDateString(),
            'status' => 'open',
        ]);

        $token = $this->postJson('/api/v1/auth/login', [
            'email' => 'staff@test.local',
            'password' => 'password',
            'device_name' => 'test',
        ])->json('token');

        $headers = $this->apiHeaders($token);

        $this->postJson('/api/v1/attendance/scan', [
            'uid' => $this->card->uid,
            'attendance_session_id' => $session->id,
        ], $headers)->assertCreated();

        $this->postJson('/api/v1/attendance/scan', [
            'uid' => $this->card->uid,
            'attendance_session_id' => $session->id,
        ], $headers)->assertStatus(422);
    }
}
