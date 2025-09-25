<?php

namespace Native\Desktop\Drivers\Electron\Updater\Contracts;

interface Updater
{
    public function environmentVariables(): array;

    public function builderOptions(): array;
}
