<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Tests\Cli\PHPCS;

use Onepix\WpStaticAnalysis\Cli\PHPCS\AbstractCommand;

/**
 * Implementation of AbstractCommand for testing purposes.
 */
class AbstractCommandImplementation extends AbstractCommand
{
    public const FAKE_BIN = 'wow';

    /**
     * @inheritDoc
     */
    protected function getBinaryName(): string
    {
        return self::FAKE_BIN;
    }
}
