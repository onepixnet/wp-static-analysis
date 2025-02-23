<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Tests\Unit\Cli\Command;

use Onepix\WpStaticAnalysis\Cli\Command\AbstractCommand;
use Onepix\WpStaticAnalysis\Cli\Factory\Process\ProcessFactoryInterface;

/**
 * Implementation of AbstractCommand for testing purposes.
 *
 * @psalm-suppress ClassMustBeFinal
 */
final class TestableAbstractCommand extends AbstractCommand
{
    public const BIN = 'wow';

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getBinaryName(): string
    {
        return self::BIN;
    }

    public function publicFindBinary(): string
    {
        return parent::findBinary();
    }

    public function getProcessFactory(): ProcessFactoryInterface
    {
        return $this->processFactory;
    }
}
