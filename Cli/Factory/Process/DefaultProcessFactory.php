<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Cli\Factory\Process;

use Onepix\WpStaticAnalysis\Cli\Factory\Process\ProcessFactoryInterface;
use Symfony\Component\Process\Process;

/**
 * Default process factory
 */
class DefaultProcessFactory implements ProcessFactoryInterface
{
    /**
     * Creates process with 5 minute timeout
     *
     * @param array<string> $command The command to run and its arguments
     */
    public function create(array $command): Process
    {
        $process = new Process($command);
        $process->setTimeout(300);
        return $process;
    }
}
