<?php

namespace App\Enums;

enum NfcModule: string
{
    case Identity = 'identity';
    case Clinic = 'clinic';
    case Attendance = 'attendance';
    case Exam = 'exam';
    case BusFare = 'bus_fare';
}
