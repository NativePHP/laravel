<?php

namespace Native\Laravel\Commands;

use Composer\InstalledVersions;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Native\Laravel\Support\Environment;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\note;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\select;

#[AsCommand(
    name: 'native:debug',
    description: 'Generate debug information required for opening an issue.',
)]
class DebugCommand extends Command implements PromptsForMissingInput
{
    protected $signature = 'native:debug {output}';

    private Collection $debugInfo;

    public function handle(): void
    {
        $this->debugInfo = collect();
        intro('Generating Debug Information...');

        $this->processEnvironment()
            ->processNativePHP();

        switch ($this->argument('output')) {
            case 'File':
                $this->outputToFile();
                break;
            case 'Clipboard':
                $this->outputToClipboard();
                break;
            case 'Console':
                $this->outputToConsole();
                break;
            default:
                error('Invalid output option specified.');
        }

        outro('Debug Information Generated.');
    }

    private function processEnvironment(): static
    {
        $locationCommand = 'which';

        if (Environment::isWindows()) {
            $locationCommand = 'where';
        }

        info('Generating Environment Data...');
        $environment = [
            'PHP' => [
                'Version' => phpversion(),
                'Path' => PHP_BINARY,
            ],
            'Laravel' => [
                'Version' => app()->version(),
                'ConfigCached' => $this->laravel->configurationIsCached(),
                'RoutesCached' => $this->laravel->routesAreCached(),
                'DebugEnabled' => $this->laravel->hasDebugModeEnabled(),
            ],
            'Node' => [
                'Version' => trim(Process::run('node -v')->output()),
                'Path' => trim(Process::run("$locationCommand node")->output()),
            ],
            'NPM' => [
                'Version' => trim(Process::run('npm -v')->output()),
                'Path' => trim(Process::run("$locationCommand npm")->output()),
            ],
            'OperatingSystem' => PHP_OS,
        ];

        $this->debugInfo->put('Environment', $environment);

        return $this;
    }

    private function processNativePHP(): static
    {
        info('Processing NativePHP Data...');
        // Get composer versions
        $versions = collect([
            'nativephp/electron' => null,
            'nativephp/laravel' => null,
            'nativephp/php-bin' => null,
        ])->mapWithKeys(function ($version, $key) {
            try {
                $version = InstalledVersions::getVersion($key);
            } catch (\OutOfBoundsException) {
                $version = 'Not Installed';
            }

            return [$key => $version];
        });

        $isNotarizationConfigured = config('nativephp-internal.notarization.apple_id')
            && config('nativephp-internal.notarization.apple_id_pass')
            && config('nativephp-internal.notarization.apple_team_id');

        $this->debugInfo->put(
            'NativePHP',
            [
                'Versions' => $versions,
                'Configuration' => [
                    'Provider' => config('nativephp.provider'),
                    'BuildHooks' => [
                        'Pre' => config('nativephp.prebuild'),
                        'Post' => config('nativephp.postbuild'),
                    ],
                    'NotarizationEnabled' => $isNotarizationConfigured,
                    'CustomPHPBinary' => config('nativephp-internal.php_binary_path') ?? false,
                ],
            ]
        );

        return $this;
    }

    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'output' => fn () => select(
                'Where would you like to output the debug information?',
                ['File', 'Clipboard', 'Console'],
                'File'
            ),
        ];
    }

    private function outputToFile(): void
    {
        File::put(base_path('nativephp_debug.json'), json_encode($this->debugInfo->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        note('Debug information saved to '.base_path('nativephp_debug.json'));
    }

    private function outputToConsole(): void
    {
        $this->output->writeln(
            print_r($this->debugInfo->toArray(), true)
        );
    }

    private function outputToClipboard(): void
    {
        $json = json_encode($this->debugInfo->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        // Copy json to clipboard
        if (Environment::isWindows()) {
            Process::run('echo '.escapeshellarg($json).' | clip');
        } elseif (Environment::isLinux()) {
            Process::run('echo '.escapeshellarg($json).' | xclip -selection clipboard');
        } else {
            Process::run('echo '.escapeshellarg($json).' | pbcopy');
        }
    }
}
