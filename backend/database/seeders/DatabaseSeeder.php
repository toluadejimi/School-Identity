<?php

namespace Database\Seeders;

use App\Models\AttendanceSession;
use App\Models\BusRoute;
use App\Models\Device;
use App\Models\Exam;
use App\Models\ExamEligibility;
use App\Models\NfcCard;
use App\Models\Student;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'admin',
            'finance',
            'clinic',
            'attendance',
            'exam',
            'transport',
            'lecturer',
            'driver',
            'logistics',
            'security',
            'vendor',
            'merchant',
            'library',
            'student',
        ];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $admin = User::firstOrCreate(
            ['email' => 'admin@school.local'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
            ],
        );
        $admin->assignRole('admin');

        $clinicStaff = User::firstOrCreate(
            ['email' => 'clinic@school.local'],
            ['name' => 'Clinic Staff', 'password' => Hash::make('password')],
        );
        $clinicStaff->assignRole('clinic');

        $transportStaff = User::firstOrCreate(
            ['email' => 'transport@school.local'],
            ['name' => 'Transport Staff', 'password' => Hash::make('password')],
        );
        $transportStaff->assignRole('transport');

        $lecturer = User::firstOrCreate(
            ['email' => 'lecturer@school.local'],
            ['name' => 'Lecturer', 'password' => Hash::make('password')],
        );
        $lecturer->assignRole('lecturer');

        $driver = User::firstOrCreate(
            ['email' => 'driver@school.local'],
            ['name' => 'Driver / Logistics', 'password' => Hash::make('password')],
        );
        $driver->assignRole('driver');

        $security = User::firstOrCreate(
            ['email' => 'security@school.local'],
            ['name' => 'Security Officer', 'password' => Hash::make('password')],
        );
        $security->assignRole('security');

        $vendor = User::firstOrCreate(
            ['email' => 'vendor@school.local'],
            ['name' => 'Vendor / Merchant', 'password' => Hash::make('password')],
        );
        $vendor->assignRole('vendor');

        $library = User::firstOrCreate(
            ['email' => 'library@school.local'],
            ['name' => 'Library Staff', 'password' => Hash::make('password')],
        );
        $library->assignRole('library');

        $studentUser = User::firstOrCreate(
            ['email' => 'student@school.local'],
            ['name' => 'Student User', 'password' => Hash::make('password')],
        );
        $studentUser->assignRole('student');

        $device = Device::firstOrCreate(
            ['device_uuid' => 'DEMO-DEVICE-001'],
            [
                'name' => 'Demo Mobile Device',
                'type' => 'mobile',
                'status' => 'active',
                'registered_by' => $admin->id,
            ],
        );

        $admin->devices()->syncWithoutDetaching([$device->id]);
        $clinicStaff->devices()->syncWithoutDetaching([$device->id]);
        $transportStaff->devices()->syncWithoutDetaching([$device->id]);
        $lecturer->devices()->syncWithoutDetaching([$device->id]);
        $driver->devices()->syncWithoutDetaching([$device->id]);
        $security->devices()->syncWithoutDetaching([$device->id]);
        $vendor->devices()->syncWithoutDetaching([$device->id]);
        $library->devices()->syncWithoutDetaching([$device->id]);
        $studentUser->devices()->syncWithoutDetaching([$device->id]);

        BusRoute::firstOrCreate(
            ['code' => 'ROUTE-A'],
            ['name' => 'Main Campus Route', 'fare_amount' => 150, 'status' => 'active'],
        );

        $student = Student::firstOrCreate(
            ['student_number' => 'STU-0001'],
            [
                'first_name' => 'Ada',
                'last_name' => 'Okafor',
                'email' => 'ada.okafor@student.school.local',
                'class_name' => 'SS2A',
                'department' => 'Science',
                'blood_group' => 'O+',
                'allergies' => 'None',
                'status' => 'active',
            ],
        );

        Wallet::firstOrCreate(
            ['student_id' => $student->id],
            ['balance' => 1000, 'currency' => 'NGN', 'status' => 'active'],
        );

        NfcCard::firstOrCreate(
            ['uid' => '04A1B2C3D4'],
            [
                'student_id' => $student->id,
                'status' => 'active',
                'issued_at' => now(),
            ],
        );

        AttendanceSession::firstOrCreate(
            ['name' => 'Morning Assembly', 'session_date' => now()->toDateString()],
            [
                'class_name' => 'All',
                'status' => 'open',
                'created_by' => $admin->id,
            ],
        );

        $exam = Exam::firstOrCreate(
            ['name' => 'Mathematics Mid-Term', 'exam_date' => now()->addDay()->toDateString()],
            [
                'subject' => 'Mathematics',
                'venue' => 'Hall A',
                'status' => 'scheduled',
            ],
        );

        ExamEligibility::firstOrCreate(
            ['exam_id' => $exam->id, 'student_id' => $student->id],
            ['eligible' => true],
        );
    }
}
