<?php

declare(strict_types=1);

namespace GrumPHP\Util;

class ComposerFile
{
    /**
     * @var array
     */
    private $configuration;

    /**
     * @var string
     */
    private $path;

    public function __construct(string $path, array $configuration)
    {
        $this->path = $path;
        $this->configuration = $configuration;
    }

    /**
     * Composer contains some logic to prepend the current bin dir to the system PATH.
     * To make sure this application works the same in CLI and Composer mode,
     * we'll have to ensure that the bin path is always prefixed.
     *
     * @see https://github.com/composer/composer/blob/1.1/src/Composer/EventDispatcher/EventDispatcher.php#L147-L160
     */
    public function ensureProjectBinDirInSystemPath(): bool
    {
        $pathStr = 'PATH';
        if (!isset($_SERVER[$pathStr]) && isset($_SERVER['Path'])) {
            $pathStr = 'Path';
        }

        if (!is_dir($this->getBinDir())) {
            return false;
        }

        // add the bin dir to the PATH to make local binaries of deps usable in scripts
        $binDir = realpath($this->getBinDir());
        $hasBindDirInPath = preg_match(
            '{(^|'.PATH_SEPARATOR.')'.preg_quote($binDir).'($|'.PATH_SEPARATOR.')}',
            $_SERVER[$pathStr]
        );

        if (!$hasBindDirInPath && isset($_SERVER[$pathStr])) {
            $_SERVER[$pathStr] = $binDir.PATH_SEPARATOR.getenv($pathStr);
            putenv($pathStr.'='.$_SERVER[$pathStr]);
        }

        return true;
    }

    public function getBinDir(): string
    {
        $binDir = $this->configuration['config']['bin-dir'] ?? null;

        if (null !== $binDir) {
            return (string) $binDir;
        }

        return 'vendor/bin';
    }

    public function getConfigDefaultPath(): ?string
    {
        return $this->configuration['extra']['grumphp']['config-default-path'] ?? null;
    }

    public function getProjectPath(): ?string
    {
        return $this->configuration['extra']['grumphp']['project-path'] ?? null;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
