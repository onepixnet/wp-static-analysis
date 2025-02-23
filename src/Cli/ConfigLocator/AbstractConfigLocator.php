<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Cli\ConfigLocator;

use RuntimeException;

abstract class AbstractConfigLocator implements ConfigLocatorInterface
{
    /** @var string Base path for resolving relative paths */
    protected string $basePath;

    public function __construct()
    {
        $this->basePath = getcwd() ?: '';
    }

    /**
     * @inheritDoc
     * @throws RuntimeException If custom standard is specified but not found
     */
    public function locate(
        ?string $customFile = null
    ): string {
        if ($customFile !== null) {
            $absoluteCustomPath = $this->getAbsolutePath($customFile);
            if (file_exists($absoluteCustomPath)) {
                return $absoluteCustomPath;
            }
            throw new RuntimeException("The specified config was not found: {$customFile}");
        }

        foreach ($this->getProjectConfigPaths() as $path) {
            $absolutePath = $this->getAbsolutePath($path);
            if (file_exists($absolutePath)) {
                return $absolutePath;
            }
        }

        return $this->getDefaultConfigPath();
    }

    /**
     * Set base path for file resolution
     *
     * @param string $basePath
     */
    public function setBasePath(string $basePath): void
    {
        $this->basePath = $basePath;
    }

    /**
     * Converting a relative path to an absolute path using a base path
     *
     * @param string $relativePath Relative path (from the project root)
     * @return string
     */
    protected function getAbsolutePath(string $relativePath): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . ltrim($relativePath, '/');
    }

    /**
     * Returns the default path to the configuration
     *
     * @return string
     */
    abstract protected function getDefaultConfigPath(): string;

    /**
     * Paths to configs relative to the location where the command to be searched runs
     *
     * @return string[]
     */
    abstract protected function getProjectConfigPaths(): array;
}
