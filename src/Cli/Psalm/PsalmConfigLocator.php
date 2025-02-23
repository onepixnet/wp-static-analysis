<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Cli\Psalm;

use Onepix\WpStaticAnalysis\Cli\ConfigLocator\AbstractConfigLocator;
use Onepix\WpStaticAnalysis\Util\Package;

final class PsalmConfigLocator extends AbstractConfigLocator
{
    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getDefaultConfigPath(): string
    {
        return Package::ROOT_DIR . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'psalm.xml';
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getProjectConfigPaths(): array
    {
        return [
            '.config/psalm.xml',
            '.config/psalm.xml.dist',
        ];
    }
}
