<?php

namespace App\Providers;

use App\Models\Student;
use App\Observers\StudentObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Older MySQL/MariaDB hosts limit indexed utf8mb4 strings to 191 chars.
        Schema::defaultStringLength(191);

        Student::observe(StudentObserver::class);
    }
}
