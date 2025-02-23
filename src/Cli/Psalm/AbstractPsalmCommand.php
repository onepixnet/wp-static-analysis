<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Cli\Psalm;

use Onepix\WpStaticAnalysis\Cli\Command\AbstractToolCommand;
use Symfony\Component\Console\Exception\LogicException;

abstract class AbstractPsalmCommand extends AbstractToolCommand
{
    /**
     * @inheritDoc
     * @throws LogicException
     */
    public function __construct(
        ?string $name = null
    ) {
        parent::__construct($name);

        $this->configLocator = new PsalmConfigLocator();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getToolConfigFlag(): string
    {
        return '--config=';
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getToolArgumentsDescription(): string
    {
        return 'Write all Psalm-related arguments with a space in them';
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getConfigOptionDescription(): string
    {
        return 'Path to custom psalm.xml relative to the project root';
    }
}
