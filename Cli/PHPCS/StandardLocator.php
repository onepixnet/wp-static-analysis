<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Cli\PHPCS;

use RuntimeException;

/**
 * Locates PHPCS standard files with fallback to default standard
 */
class StandardLocator
{
    private const DEFAULT_STANDARD_NAME = 'WpOnepixStandard';
    private const PROJECT_STANDARD_PATHS = [
        '.config/.phpcs.xml',
        '.config/phpcs.xml',
        '.config/.phpcs.xml.dist',
        '.config/phpcs.xml.dist',
    ];

    /** @var string|null Base path for resolving relative paths */
    private ?string $basePath;

    public function __construct()
    {
        $this->basePath = getcwd();
    }

    /**
     * Set base path for file resolution
     *
     * @param string|null $basePath
     */
    public function setBasePath(?string $basePath): void
    {
        $this->basePath = $basePath;
    }

    /**
     * Locate standard file
     *
     * @param string|null $customStandard Custom standard path (relative to base path)
     * @return string
     *
     * @throws RuntimeException If custom standard is specified but not found
     */
    public function locate(
        ?string $customStandard = null
    ): string {
        if ($customStandard !== null) {
            $absoluteCustomPath = $this->getAbsolutePath($customStandard);
            if (file_exists($absoluteCustomPath)) {
                return $absoluteCustomPath;
            }
            throw new RuntimeException("Custom standard not found: {$customStandard}");
        }

        foreach (self::PROJECT_STANDARD_PATHS as $path) {
            $absolutePath = $this->getAbsolutePath($path);
            if (file_exists($absolutePath)) {
                return $absolutePath;
            }
        }

        return self::DEFAULT_STANDARD_NAME;
    }

    /**
     * Converting a relative path to an absolute path using a base path
     *
     * @param string $relativePath Relative path (from the project root)
     * @return string
     */
    private function getAbsolutePath(string $relativePath): string
    {
        return $this->basePath . '/' . ltrim($relativePath, '/');
    }
}
