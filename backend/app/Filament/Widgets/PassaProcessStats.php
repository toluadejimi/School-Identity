<?php

namespace App\Filament\Widgets;

use App\Models\AttendanceRecord;
use App\Models\CardTap;
use App\Models\ClinicVisit;
use App\Models\ExamEligibility;
use App\Models\FareTransaction;
use App\Models\NfcCard;
use App\Models\Student;
use App\Models\Wallet;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PassaProcessStats extends StatsOverviewWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $heading = 'Passa Card operations';

    protected ?string $description = 'Live management snapshot for identity, card, wallet, and campus workflows.';

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today = now()->toDateString();

        return [
            Stat::make('Students', number_format(Student::query()->count()))
                ->description(number_format(Student::query()->whereDate('created_at', $today)->count()) . ' registered today')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->icon('heroicon-o-user-group'),
            Stat::make('Active Passa Cards', number_format(NfcCard::query()->where('status', 'active')->count()))
                ->description(number_format(NfcCard::query()->whereNull('student_id')->count()) . ' unassigned cards')
                ->color('success')
                ->icon('heroicon-o-credit-card'),
            Stat::make('Wallet Balance', 'NGN ' . number_format((float) Wallet::query()->sum('balance'), 2))
                ->description(number_format(FareTransaction::query()->whereDate('created_at', $today)->count()) . ' fare payments today')
                ->color('warning')
                ->icon('heroicon-o-banknotes'),
            Stat::make('Scans Today', number_format(CardTap::query()->whereDate('tapped_at', $today)->count()))
                ->description('Identity lookups across devices')
                ->color('info')
                ->icon('heroicon-o-signal'),
            Stat::make('Clinic Visits', number_format(ClinicVisit::query()->whereDate('checked_in_at', $today)->count()))
                ->description(number_format(ClinicVisit::query()->where('status', 'open')->count()) . ' currently open')
                ->color('danger')
                ->icon('heroicon-o-heart'),
            Stat::make('Attendance Records', number_format(AttendanceRecord::query()->whereDate('recorded_at', $today)->count()))
                ->description(number_format(ExamEligibility::query()->where('eligible', true)->count()) . ' exam eligible records')
                ->color('primary')
                ->icon('heroicon-o-academic-cap'),
        ];
    }
}
