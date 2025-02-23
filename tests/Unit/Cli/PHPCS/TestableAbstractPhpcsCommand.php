<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Tests\Unit\Cli\PHPCS;

use Onepix\WpStaticAnalysis\Cli\ConfigLocator\ConfigLocatorInterface;
use Onepix\WpStaticAnalysis\Cli\PHPCS\AbstractPhpcsCommand;

final class TestableAbstractPhpcsCommand extends AbstractPhpcsCommand
{
    public const BIN = 'test';

    /**
     * @return string
     *
     * @psalm-return 'test'
     */
    #[\Override]
    protected function getBinaryName(): string
    {
        return self::BIN;
    }

    /**
     * @return ConfigLocatorInterface
     */
    public function getConfigLocator(): ConfigLocatorInterface
    {
        return $this->configLocator;
    }

    /**
     * @return string
     */
    #[\Override]
    public function getConfigOptionName(): string
    {
        return parent::getConfigOptionName();
    }

    /**
     * @return string
     */
    #[\Override]
    public function getToolConfigFlag(): string
    {
        return parent::getToolConfigFlag();
    }

    /**
     * @return string
     */
    #[\Override]
    public function getToolArgumentsDescription(): string
    {
        return parent::getToolArgumentsDescription();
    }

    /**
     * @return string
     */
    #[\Override]
    public function getConfigOptionDescription(): string
    {
        return parent::getConfigOptionDescription();
    }
}
