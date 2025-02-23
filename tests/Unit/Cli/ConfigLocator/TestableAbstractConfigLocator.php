<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Tests\Unit\Cli\ConfigLocator;

use Onepix\WpStaticAnalysis\Cli\ConfigLocator\AbstractConfigLocator;

final class TestableAbstractConfigLocator extends AbstractConfigLocator
{
    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getProjectConfigPaths(): array
    {
        return [
            '.config/test.xml',
        ];
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getDefaultConfigPath(): string
    {
        return 'defaultPath';
    }
}
