<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Cli\PHPCS;

use Onepix\WpStaticAnalysis\Cli\Command\AbstractToolCommand;
use Symfony\Component\Console\Exception\LogicException;

abstract class AbstractPhpcsCommand extends AbstractToolCommand
{
    /**
     * @inheritDoc
     * @throws LogicException
     */
    public function __construct(?string $name = null)
    {
        parent::__construct($name);

        $this->configLocator = new StandardLocator();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getConfigOptionName(): string
    {
        return 'ruleset';
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getToolConfigFlag(): string
    {
        return '--standard=';
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getToolArgumentsDescription(): string
    {
        return 'Spell out any arguments related to PHPCS with a space';
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getConfigOptionDescription(): string
    {
        return 'Path to custom phpcs.xml relative to project root';
    }
}
