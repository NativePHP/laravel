<?php

namespace Native\Desktop\Builder;

use Native\Desktop\Builder\Traits\CleansEnvFile;
use Native\Desktop\Builder\Traits\CopiesBundleToBuildDirectory;
use Native\Desktop\Builder\Traits\CopiesCertificateAuthority;
use Native\Desktop\Builder\Traits\CopiesToBuildDirectory;
use Native\Desktop\Builder\Traits\HasPreAndPostProcessing;
use Native\Desktop\Builder\Traits\LocatesPhpBinary;
use Native\Desktop\Builder\Traits\PrunesVendorDirectory;
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
        return Path::join($this->sourcePath, $path);
    }
}
