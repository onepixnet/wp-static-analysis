<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Tests\Unit\Cli\PHPCS;

use Onepix\WpStaticAnalysis\Cli\PHPCS\AbstractCommand;

/**
 * Implementation of AbstractCommand for testing purposes.
 *
 * @psalm-suppress ClassMustBeFinal
 */
final class AbstractCommandImplementation extends AbstractCommand
{
    public const FAKE_BIN = 'wow';

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getBinaryName(): string
    {
        return self::FAKE_BIN;
    }
}
