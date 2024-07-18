<?php

namespace Native\Electron\Traits;

use function Laravel\Prompts\select;

trait OsAndArch
{
    // Available OS in Build and Publish commands
    protected function getAvailableOs(): array
    {
        return $this->availableOs;
    }

    protected function getDefaultOs(): string
    {
        return match (PHP_OS_FAMILY) {
            'Windows' => 'win',
            'Darwin' => 'mac',
            'Linux' => 'linux',
            default => 'all',
        };
    }

    protected function selectOs(?string $os): string
    {
        $os = $os ?? false;
        if (! in_array($this->argument('os'), $this->getAvailableOs()) || ! $os) {
            $os = select(
                label: 'Please select the operating system to build for',
                options: $this->getAvailableOs(),
                default: $this->getDefaultOs(),
            );
        }

        return $os;
    }

    /**
     * Get Arch for selected os
     *
     * Make this dynamic at some point
     */
    protected function getArchitectureForOs(string $os): array
    {
        $archs = match ($os) {
            'win' => ['x64'],
            'mac' => ['x86', 'arm64'],
            'linux' => ['x64']
        };

        return [...$archs, 'all'];
    }

    // Depends on the currenty available php executables
    protected function selectArchitectureForOs(string $os, ?string $arch): string
    {
        $arch = $arch ?? false;
        if (! in_array($this->argument('arch'), ($a = $this->getArchitectureForOs($os))) || ! $arch) {
            $arch = select(
                label: 'Please select Processor Architecture',
                options: $a,
                default: 'all'
            );
        }

        return $arch;
    }
}
