<?php

namespace App\Observers;

use App\Models\Student;
use App\Models\Wallet;

class StudentObserver
{
    public function created(Student $student): void
    {
        Wallet::firstOrCreate(
            ['student_id' => $student->id],
            ['balance' => 0, 'currency' => 'NGN', 'status' => 'active'],
        );
    }
}
