<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Cli\Factory\Process;

use Symfony\Component\Process\Process;

/**
 * Factory interface for creating Process instances
 */
interface ProcessFactoryInterface
{
    /**
     * Create configured Process instance
     *
     * @param array<string> $command The command to run and its arguments
     */
    public function create(array $command): Process;
}
