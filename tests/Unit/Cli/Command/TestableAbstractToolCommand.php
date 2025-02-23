<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Tests\Unit\Cli\Command;

use Onepix\WpStaticAnalysis\Cli\Command\AbstractToolCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class TestableAbstractToolCommand extends AbstractToolCommand
{
    public const BINARY_NAME = 'test-tool';
    public const CONFIG_FLAG = '--config=';

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getBinaryName(): string
    {
        return self::BINARY_NAME;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getToolConfigFlag(): string
    {
        return self::CONFIG_FLAG;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getConfigOptionDescription(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getToolArgumentsDescription(): string
    {
        return '';
    }

    public function publicExecute(InputInterface $input, OutputInterface $output): int
    {
//        return $this->run($input, $output);
        return $this->execute($input, $output);
    }
}
