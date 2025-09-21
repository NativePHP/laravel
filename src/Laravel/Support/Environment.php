<?php

namespace Native\Laravel\Support;

class Environment
{
    public static function isWindows(): bool
    {
        return PHP_OS_FAMILY === 'Windows';
    }

    public static function isLinux(): bool
    {
        return PHP_OS_FAMILY === 'Linux';
    }

    public static function isMac(): bool
    {
        return PHP_OS_FAMILY === 'Darwin';
    }

    public static function isUnknown(): bool
    {
        return PHP_OS_FAMILY === 'Unknown';
    }

    public static function isUnixLike(): bool
    {
        return static::isLinux() || static::isMac();
    }
}
