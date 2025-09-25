<?php

namespace Native\Support;

use Native\Support\Traits\CleansEnvFile;
use Native\Support\Traits\CopiesBundleToBuildDirectory;
use Native\Support\Traits\CopiesCertificateAuthority;
use Native\Support\Traits\CopiesToBuildDirectory;
use Native\Support\Traits\HasPreAndPostProcessing;
use Native\Support\Traits\LocatesPhpBinary;
use Native\Support\Traits\PrunesVendorDirectory;
use Symfony\Component\Filesystem\Path;

class Builder
{
    use CleansEnvFile;
    use CopiesBundleToBuildDirectory;
    use CopiesCertificateAuthority;
    use CopiesToBuildDirectory;
    use HasPreAndPostProcessing;
    use LocatesPhpBinary;
    use PrunesVendorDirectory;

    public function __construct(
        private string $buildPath,
        private ?string $sourcePath = null,
    ) {

        $this->sourcePath = $sourcePath
            ? $sourcePath
            : base_path();
    }

    public static function make(
        string $buildPath,
        ?string $sourcePath = null
    ) {
        return new self($buildPath, $sourcePath);
    }

    public function buildPath(string $path = ''): string
    {
        return Path::join($this->buildPath, $path);
    }

    public function sourcePath(string $path = ''): string
    {
        return base_path($path);
    }
}
