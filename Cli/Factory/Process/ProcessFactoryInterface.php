<?php
declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Cli\Factory\Process;

use Symfony\Component\Process\Process;

interface ProcessFactoryInterface
{
    public function create(array $command): Process;
}
