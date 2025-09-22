<?php

namespace Native\Electron\Traits;

use Illuminate\Support\Facades\Http;

use function Laravel\Prompts\intro;

trait HandlesBifrost
{
    private function baseUrl(): string
    {
        return str(config('nativephp-internal.bifrost.host'))->finish('/');
    }

    private function checkAuthenticated()
    {
        intro('Checking authentication…');

        return Http::acceptJson()
            ->withToken(config('nativephp-internal.bifrost.token'))
            ->get($this->baseUrl().'api/v1/auth/user')->successful();
    }

    private function checkForBifrostToken()
    {
        if (! config('nativephp-internal.bifrost.token')) {
            $this->line('');
            $this->warn('No BIFROST_TOKEN found. Please login first.');
            $this->line('');
            $this->line('Run: php artisan bifrost:login');
            $this->line('');

            return false;
        }

        return true;
    }

    private function checkForBifrostProject()
    {
        if (! config('nativephp-internal.bifrost.project')) {
            $this->line('');
            $this->warn('No BIFROST_PROJECT found. Please select a project first.');
            $this->line('');
            $this->line('Run: php artisan bifrost:init');
            $this->line('');

            return false;
        }

        return true;
    }
}