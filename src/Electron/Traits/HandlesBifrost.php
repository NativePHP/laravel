<?php

namespace Native\Electron\Traits;

use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

use function Laravel\Prompts\intro;

trait HandlesBifrost
{
    private function baseUrl(): string
    {
        return str(config('nativephp-internal.bifrost.host'))->finish('/');
    }

    private function checkAuthenticated(): bool
    {
        intro('Checking authentication…');

        try {
            return Http::acceptJson()
                ->withToken(config('nativephp-internal.bifrost.token'))
                ->get($this->baseUrl().'api/v1/auth/user')->successful();
        } catch (Exception $e) {
            $this->error('Network error: '.$e->getMessage());

            return false;
        }
    }

    private function checkForBifrostToken(): bool
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

    private function checkForBifrostProject(): bool
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

    /**
     * Validates authentication and returns user data
     *
     * @throws Exception
     */
    private function validateAuthAndGetUser(): array
    {
        if (! $this->checkForBifrostToken()) {
            throw new Exception('No BIFROST_TOKEN found. Please login first.');
        }

        try {
            $response = Http::acceptJson()
                ->withToken(config('nativephp-internal.bifrost.token'))
                ->get($this->baseUrl().'api/v1/auth/user');

            if ($response->failed()) {
                throw new Exception('Invalid API token. Please login again.');
            }

            $data = $response->json();

            if (! isset($data['data'])) {
                throw new Exception('Invalid API response format.');
            }

            return $data['data'];
        } catch (Exception $e) {
            throw new Exception('Authentication failed: '.$e->getMessage());
        }
    }

    private function getCurrentTeamSlug(): ?string
    {
        try {
            $user = $this->validateAuthAndGetUser();

            return $user['current_team']['slug'] ?? null;
        } catch (Exception $e) {
            return null;
        }
    }

    protected function makeApiRequest(string $method, string $endpoint, array $data = []): Response
    {
        try {
            $request = Http::acceptJson()
                ->withToken(config('nativephp-internal.bifrost.token'));

            return match (strtoupper($method)) {
                'GET' => $request->get($this->baseUrl().$endpoint),
                'POST' => $request->post($this->baseUrl().$endpoint, $data),
                'PUT' => $request->put($this->baseUrl().$endpoint, $data),
                'DELETE' => $request->delete($this->baseUrl().$endpoint),
                default => throw new Exception("Unsupported HTTP method: {$method}")
            };
        } catch (Exception $e) {
            throw new Exception("API request failed: {$e->getMessage()}");
        }
    }
}
