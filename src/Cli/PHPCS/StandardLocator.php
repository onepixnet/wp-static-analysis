<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Cli\PHPCS;

use Onepix\WpStaticAnalysis\Cli\ConfigLocator\AbstractConfigLocator;

/**
 * Locates PHPCS standard files with fallback to default standard
 */
final class StandardLocator extends AbstractConfigLocator
{
    /**
     * @inheritDoc
     */
    protected function getProjectConfigPaths(): array
    {
        return [
            '.config/.phpcs.xml',
            '.config/phpcs.xml',
            '.config/.phpcs.xml.dist',
            '.config/phpcs.xml.dist',
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultConfigPath(): string
    {
        return 'WpOnepixStandard';
    }
}
