<?php

namespace Native\Laravel\Commands;

use Composer\InstalledVersions;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use function Laravel\Prompts\error;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\select;

class DebugCommand extends Command implements PromptsForMissingInput
{
    protected $signature = 'native:debug {output}';
    protected $description = 'Generate debug information required for opening an issue.';

    private Collection $debugInfo;

    public function handle(): void
    {
        $this->debugInfo = collect();
        intro('Generating Debug Information...');

        $this->processEnvironment()
            ->processNativePHP()
            ->processErrorLog();

        switch ($this->argument('output')) {
            case 'File':
                $this->outputToFile();
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
        info('Generating Environment Data...');
        $environment = [
            'PHP' => [
                'Version' => phpversion(),
                'Path' => PHP_BINARY,
            ],
            'Laravel' => [
                'Version' => app()->version(),
                'ConfigCached' => file_exists($this->laravel->getCachedConfigPath()),
                'DebugEnabled' => $this->laravel->hasDebugModeEnabled()
            ],
            'Node' => [
                'Version' => trim(Process::run('node -v')->output()),
                'Path' => trim(Process::run('which node')->output()),
                'NPM' => trim(Process::run('npm -v')->output()),
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

        $isNotarisationConfigured = env('NATIVEPHP_APPLE_ID')
            && env('NATIVEPHP_APPLE_ID_PASS')
            && env('NATIVEPHP_APPLE_TEAM_ID');

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
                    'NotarizationEnabled' => $isNotarisationConfigured,
                    'CustomPHPBinary' => env('NATIVEPHP_PHP_BINARY_PATH') ?: false,
                ],
            ]
        );

        return $this;
    }

    private function processErrorLog(): void
    {
        info('Processing Error Log Data...');
        $errorLog = file_exists($logPath = storage_path('logs/laravel.log'))
            ? file_get_contents($logPath)
            : 'No logs found.';

        // Process each line as a single array element
        $errorLog = explode(PHP_EOL, $errorLog);
        $errorCount = 0;
        $errors = [];

        $currentLine = '';
        foreach ($errorLog as $line) {
            if ($errorCount === 5) {
                break;
            }

            // Check if string starts with date format Y-m-d H:i:s in square brackets
            if (preg_match('/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]/', $line)) {
                if (!empty($currentLine)) {
                    $errors[] = $currentLine;
                    $currentLine = '';
                    $errorCount++;
                }
            }

            $currentLine .= $line . PHP_EOL;
        }

        if (!empty($currentLine)) {
            $errors[] = $currentLine;
        }

        $this->debugInfo->put('ErrorLog', $errors);
    }

    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'output' => fn () => select(
                'Where would you like to output the debug information?',
                ['File', 'Console'],
                'File'
            )
        ];
    }

    private function outputToFile(): void
    {
        File::put(base_path('nativephp_debug.json'), json_encode($this->debugInfo->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        note('Debug information saved to ' . base_path('nativephp_debug.json'));
    }

    private function outputToConsole(): void
    {
        $this->output->writeln(
            print_r($this->debugInfo->toArray(), true)
        );
    }
}
