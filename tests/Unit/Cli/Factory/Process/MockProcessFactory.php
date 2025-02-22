<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Tests\Unit\Cli\Factory\Process;

use Onepix\WpStaticAnalysis\Cli\Factory\Process\ProcessFactoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Process\Process;

/**
 * Mock factory that returns predefined Process instances
 * Used for testing classes that depend on ProcessFactoryInterface
 */
final class MockProcessFactory implements ProcessFactoryInterface
{
    private Process $mock;

    public function __construct(Process $mock)
    {
        $this->mock = $mock;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function create(array $command): Process
    {
        return $this->mock;
    }
}
