<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AttendanceSessions\AttendanceSessionResource;
use App\Filament\Resources\BusRoutes\BusRouteResource;
use App\Filament\Resources\ClinicVisits\ClinicVisitResource;
use App\Filament\Resources\Devices\DeviceResource;
use App\Filament\Resources\ExamEligibilities\ExamEligibilityResource;
use App\Filament\Resources\NfcCards\NfcCardResource;
use App\Filament\Resources\Students\StudentResource;
use App\Filament\Resources\Wallets\WalletResource;
use Filament\Widgets\Widget;

class PassaProcessDashboard extends Widget
{
    protected static bool $isDiscovered = false;

    protected string $view = 'filament.widgets.passa-process-dashboard';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    protected function getViewData(): array
    {
        return [
            'stages' => [
                ['label' => 'Register', 'title' => 'Create student profile', 'text' => 'Capture biodata, guardian, academic, medical, and photo records.', 'url' => StudentResource::getUrl('create')],
                ['label' => 'Issue', 'title' => 'Bind Passa Card UID', 'text' => 'Attach or replace the UID-only card that unlocks all campus workflows.', 'url' => NfcCardResource::getUrl('create')],
                ['label' => 'Operate', 'title' => 'Scan across departments', 'text' => 'Clinic, lecture, exam, security, vendors, and transport all resolve from one identity.', 'url' => DeviceResource::getUrl('index')],
                ['label' => 'Audit', 'title' => 'Review activity', 'text' => 'Monitor wallet balances, fare activity, attendance, and card status changes.', 'url' => WalletResource::getUrl('index')],
            ],
            'modules' => [
                ['name' => 'Students', 'description' => 'Profiles and registration', 'url' => StudentResource::getUrl('index')],
                ['name' => 'Passa Cards', 'description' => 'UID issuance and status', 'url' => NfcCardResource::getUrl('index')],
                ['name' => 'Clinic', 'description' => 'Visits and treatment notes', 'url' => ClinicVisitResource::getUrl('index')],
                ['name' => 'Attendance', 'description' => 'Lecture sessions and records', 'url' => AttendanceSessionResource::getUrl('index')],
                ['name' => 'Exams', 'description' => 'Eligibility checks', 'url' => ExamEligibilityResource::getUrl('index')],
                ['name' => 'Transport', 'description' => 'Routes and fares', 'url' => BusRouteResource::getUrl('index')],
                ['name' => 'Wallets', 'description' => 'Balances and payment state', 'url' => WalletResource::getUrl('index')],
                ['name' => 'Devices', 'description' => 'Trusted scanners', 'url' => DeviceResource::getUrl('index')],
            ],
        ];
    }
}
