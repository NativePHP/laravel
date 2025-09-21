<?php

namespace Native\Electron\Updater\Contracts;

interface Updater
{
    public function environmentVariables(): array;

    public function builderOptions(): array;
}
