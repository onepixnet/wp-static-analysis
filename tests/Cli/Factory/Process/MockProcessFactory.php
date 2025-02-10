<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Tests\Cli\Factory\Process;

use Onepix\WpStaticAnalysis\Cli\Factory\Process\ProcessFactoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Process\Process;

/**
 * Mock factory that returns predefined Process instances
 * Used for testing classes that depend on ProcessFactoryInterface
 */
class MockProcessFactory implements ProcessFactoryInterface
{
    private Process|MockObject $mock;

    public function __construct(Process|MockObject $mock)
    {
        $this->mock = $mock;
    }

    /** @inheritDoc */
    public function create(array $command): Process
    {
        return $this->mock;
    }
}
