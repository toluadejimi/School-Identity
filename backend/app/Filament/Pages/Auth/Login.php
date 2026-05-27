<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    protected string $view = 'filament.auth.login';

    protected Width | string | null $maxWidth = Width::Full;

    public function getTitle(): string | Htmlable
    {
        return 'Admin Login | School Identity Passa';
    }

    public function getHeading(): string | Htmlable | null
    {
        return 'Welcome back';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Sign in to manage Passa Card operations.';
    }

    public function hasLogo(): bool
    {
        return false;
    }
}
