<?php

namespace Native\Electron\Traits;

use Illuminate\Support\Facades\Http;

use function Laravel\Prompts\intro;

trait HandlesZephpyr
{
    private function baseUrl(): string
    {
        return str(config('nativephp-internal.zephpyr.host'))->finish('/');
    }

    private function checkAuthenticated()
    {
        intro('Checking authentication…');

        return Http::acceptJson()
            ->withToken(config('nativephp-internal.zephpyr.token'))
            ->get($this->baseUrl().'api/v1/user')->successful();
    }

    private function checkForZephpyrKey()
    {
        $this->key = config('nativephp-internal.zephpyr.key');

        if (! $this->key) {
            $this->line('');
            $this->warn('No ZEPHPYR_KEY found. Cannot bundle!');
            $this->line('');
            $this->line('Add this app\'s ZEPHPYR_KEY to its .env file:');
            $this->line(base_path('.env'));
            $this->line('');
            $this->info('Not set up with Zephpyr yet? Secure your NativePHP app builds and more!');
            $this->info('Check out '.$this->baseUrl().'');
            $this->line('');

            return false;
        }

        return true;
    }

    private function checkForZephpyrToken()
    {
        if (! config('nativephp-internal.zephpyr.token')) {
            $this->line('');
            $this->warn('No ZEPHPYR_TOKEN found. Cannot bundle!');
            $this->line('');
            $this->line('Add your Zephpyr API token to your .env file (ZEPHPYR_TOKEN):');
            $this->line(base_path('.env'));
            $this->line('');
            $this->info('Not set up with Zephpyr yet? Secure your NativePHP app builds and more!');
            $this->info('Check out '.$this->baseUrl().'');
            $this->line('');

            return false;
        }

        return true;
    }
}
