<?php
declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Tests\Cli\Factory\Process;

use Onepix\WpStaticAnalysis\Cli\Factory\Process\ProcessFactoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Process\Process;

class MockProcessFactory implements ProcessFactoryInterface
{
    private Process|MockObject $mock;

    public function __construct(Process|MockObject $mock)
    {
        $this->mock = $mock;
    }

    public function create(array $command): Process
    {
        return $this->mock;
    }
}
