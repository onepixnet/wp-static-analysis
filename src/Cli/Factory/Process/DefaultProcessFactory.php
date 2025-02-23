<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Cli\Factory\Process;

use Override;
use Symfony\Component\Process\Exception\InvalidArgumentException;
use Symfony\Component\Process\Exception\LogicException;
use Symfony\Component\Process\Process;

/**
 * Default process factory
 */
final class DefaultProcessFactory implements ProcessFactoryInterface
{
    /**
     * @inheritDoc
     *
     * @return Process
     * @throws InvalidArgumentException
     * @throws LogicException
     * */
    #[Override]
    public function create(array $command, ?string $cwd = null, ?array $env = null): Process
    {
        $process = new Process($command, $cwd, $env);
        $process->setTimeout(300);
        return $process;
    }
}
