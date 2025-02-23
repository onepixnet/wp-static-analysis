<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Cli\PHPCS;

use Onepix\WpStaticAnalysis\Cli\PHPCS\AbstractPhpcsCommand;
use Symfony\Component\Console\Attribute\AsCommand;

/**
 * Command for running PHP_CodeSniffer
 */
#[AsCommand(
    name: 'phpcs',
    description: 'Run PHP_CodeSniffer with custom standards'
)]
final class PhpcsCommand extends AbstractPhpcsCommand
{
    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getBinaryName(): string
    {
        return 'phpcs';
    }
}
