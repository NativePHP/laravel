<?php

namespace Native\Electron\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Native\Electron\Concerns\LocatesPhpBinary;
use Native\Electron\Traits\OsAndArch;

class PublishCommand extends Command
{
    use LocatesPhpBinary;
    use OsAndArch;

    protected $signature = 'native:publish
        {os? : The operating system to build for (linux, mac, win)}
        {arch? : The Processor Architecture to build for (x64, x86, arm64)}';

    protected array $availableOs = ['win', 'linux', 'mac'];

    public function handle(): void
    {
        $this->info('Building and publishing NativePHP app…');

        $os = $this->selectOs($this->argument('os'));

        $arch = $this->selectArchitectureForOs($os, $this->argument('arch'));

        Artisan::call('native:build', ['os' => $os, 'arch' => $arch, '--publish' => true], $this->output);
    }
}
