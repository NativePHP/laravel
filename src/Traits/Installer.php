<?php

namespace Native\Electron\Traits;

trait Installer
{
    protected function installNpmDependencies()
    {
        $this->executeCommand('npm set progress=false && npm install', $this->nativePhpPath());
    }

    protected function installYarnDependencies()
    {
        $this->executeCommand('yarn install', $this->nativePhpPath());
    }

    protected function installPnpmDependencies()
    {
        $this->executeCommand('pnpm install', $this->nativePhpPath());
    }
}
