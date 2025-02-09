<?php
declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Cli\Factory\Process;

use Onepix\WpStaticAnalysis\Cli\Factory\Process\ProcessFactoryInterface;
use Symfony\Component\Process\Process;

class DefaultProcessFactory implements ProcessFactoryInterface
{
    public function create(array $command): Process
    {
        $process = new Process($command);
        $process->setTimeout(300);
        return $process;
    }
}
