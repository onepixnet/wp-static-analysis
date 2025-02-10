<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Cli\PHPCS;

use RuntimeException;

/**
 * Locates PHPCS ruleset files with fallback to default standard
 */
class RulesetLocator
{
    private const DEFAULT_STANDARD_NAME = 'WpOnepixStandard';
    private const PROJECT_RULESET_PATH = 'config/ruleset.xml';

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
     * Locate ruleset file with fallback logic
     *
     * @param string|null $customRuleset Custom ruleset path (relative to base path)
     * @return string
     *
     * @throws RuntimeException If custom ruleset is specified but not found
     */
    public function locate(
        ?string $customRuleset = null
    ): string {
        if ($customRuleset !== null) {
            $absoluteCustomPath = $this->getAbsolutePath($customRuleset);
            if (file_exists($absoluteCustomPath)) {
                return $absoluteCustomPath;
            }
            throw new RuntimeException("Custom ruleset not found: {$customRuleset}");
        }

        $projectRulesetPath = $this->getAbsolutePath(self::PROJECT_RULESET_PATH);
        if (file_exists($projectRulesetPath)) {
            return $projectRulesetPath;
        }

        return self::DEFAULT_STANDARD_NAME;
    }

    /**
     * Convert relative path to absolute using base path
     *
     * @param string $relativePath Relative path (from the project root)
     * @return string
     */
    private function getAbsolutePath(string $relativePath): string
    {
        return $this->basePath . '/' . ltrim($relativePath, '/');
    }
}
