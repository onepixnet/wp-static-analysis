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
     * @param string|null $cwd
     * @param array|null $env
     */
    public function create(array $command, ?string $cwd = null, ?array $env = null): Process;
}
