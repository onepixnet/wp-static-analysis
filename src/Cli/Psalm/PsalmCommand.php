<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Cli\Psalm;

use Onepix\WpStaticAnalysis\Cli\Psalm\AbstractPsalmCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'psalm',
    description: 'Run Psalm with default config'
)]
final class PsalmCommand extends AbstractPsalmCommand
{
    protected function getBinaryName(): string
    {
        return 'psalm';
    }
}
