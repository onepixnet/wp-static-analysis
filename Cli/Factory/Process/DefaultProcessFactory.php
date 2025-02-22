<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Cli\Factory\Process;

use InvalidArgumentException;
use LogicException;
use Override;
use Symfony\Component\Process\Process;

/**
 * Default process factory
 */
final class DefaultProcessFactory implements ProcessFactoryInterface
{
    /**
     *  Creates process with 5 minute timeout
     *
     * @param array<string> $command The command to run and its arguments
     *
     * @return Process
     * @throws \Symfony\Component\Process\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Process\Exception\LogicException
     * */
    #[Override]
    public function create(array $command): Process
    {
        $process = new Process($command);
        $process->setTimeout(300);
        return $process;
    }
}
