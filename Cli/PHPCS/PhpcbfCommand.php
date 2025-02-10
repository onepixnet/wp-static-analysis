<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Cli\PHPCS;

use Symfony\Component\Console\Attribute\AsCommand;

/**
 * Command for running PHP_CodeSniffer Beautifier and Fixer
 */
#[AsCommand(
    name: 'phpcbf',
    description: 'Run PHP_CodeSniffer Beautifier and Fixer with custom standards'
)]
final class PhpcbfCommand extends AbstractCommand
{
    /**
     * @inheritDoc
     */
    protected function getBinaryName(): string
    {
        return 'phpcbf';
    }
}
