<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Cli\PHPCS;

use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'phpcbf',
    description: 'Run PHP_CodeSniffer Beautifier and Fixer with custom standards'
)]
class PhpcbfCommand extends AbstractCommand
{
    protected function getBinaryName(): string
    {
        return 'phpcbf';
    }
}
