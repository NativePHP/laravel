<?php

namespace Native\Desktop\Support;

use Composer\InstalledVersions;
use RuntimeException;
use Symfony\Component\Filesystem\Path;

use function Laravel\Prompts\note;

class Composer
{
    public static function desktopPackagePath(string $path = '')
    {
        return self::vendorPath("nativephp/desktop/{$path}");
    }

    public static function phpPackagePath(string $path = '')
    {
        return self::vendorPath("nativephp/php-bin/{$path}");
    }

    public static function vendorPath(string $path = '')
    {
        $vendorPath = realpath(InstalledVersions::getRootPackage()['install_path'].'/vendor');

        return Path::join($vendorPath, $path);
    }

    public static function installScripts()
    {
        $composer = json_decode(file_get_contents(base_path('composer.json')));
        throw_unless($composer, RuntimeException::class, "composer.json couldn't be parsed");

        self::installDevScript($composer);
        self::installUpdateScript($composer);
    }

    private static function installDevScript(object $composer)
    {
        $composerScripts = $composer->scripts ?? (object) [];

        info('Installing `composer native:dev` script alias...');

        if ($composerScripts->{'native:dev'} ?? false) {
            note('native:dev script already installed... skipping.');

            return;
        }

        $composerScripts->{'native:dev'} = [
            'Composer\\Config::disableProcessTimeout',
            'npx concurrently -k -c "#93c5fd,#c4b5fd" "php artisan native:serve --no-interaction" "npm run dev" --names=app,vite',
        ];

        data_set($composer, 'scripts', $composerScripts);

        file_put_contents(
            base_path('composer.json'),
            json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL
        );

        note('native:dev script installed!');
    }

    private static function installUpdateScript(object $composer)
    {
        $postUpdateScripts = data_get($composer, 'scripts.post-update-cmd', []);

        info('Installing `native:install` post-update-cmd script');

        foreach ($postUpdateScripts as $script) {
            if (str_contains($script, 'native:install')) {
                note('native:install script already present in post-update-cmd... skipping.');

                return;
            }
        }

        $postUpdateScripts[] = '@php artisan native:install --force --quiet';

        data_set($composer, 'scripts.post-update-cmd', $postUpdateScripts);

        file_put_contents(
            base_path('composer.json'),
            json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL
        );

        note('post-update-cmd script installed!');
    }
}
