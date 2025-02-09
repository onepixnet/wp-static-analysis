<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Cli\PHPCS;

use RuntimeException;

class RulesetLocator
{
    private const DEFAULT_STANDARD_NAME = 'WpOnepixStandard';
    private const PROJECT_RULESET_PATH = 'config/ruleset.xml';

    /**
     * @var string|null
     */
    private ?string $basePath;

    public function __construct()
    {
        $this->basePath = getcwd();
    }

    /**
     * @param string|null $basePath
     */
    public function setBasePath(?string $basePath): void
    {
        $this->basePath = $basePath;
    }

    /**
     * Returns the path to the configuration file
     *
     * @param string|null $customRuleset User path to the configuration (relative to the project root)
     * @return string
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
     * Converts a relative path to an absolute path.
     *
     * @param string $relativePath Relative path (from the project root)
     * @return string
     */
    private function getAbsolutePath(string $relativePath): string
    {
        return $this->basePath . '/' . ltrim($relativePath, '/');
    }
}
