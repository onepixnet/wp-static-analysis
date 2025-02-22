<?php

namespace Onepix\WpStaticAnalysis\Tests\Unit\Cli\PHPCS;

use Onepix\WpStaticAnalysis\Cli\PHPCS\AbstractCommand;
use Onepix\WpStaticAnalysis\Cli\PHPCS\PhpcbfCommand;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;

/**
 * Test class for PhpcbfCommand.
 */
#[CoversClass(PhpcbfCommand::class)]
final class PhpcbfCommandTest extends TestCase
{
    private const BIN = 'phpcbf';

    private AbstractCommand $command;

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->command = new PhpcbfCommand();
    }

    /**
     * Tests the getBinaryName method
     *
     * @return void
     * @throws ReflectionException
     */
    public function testGetBinaryName(): void
    {
        $method = new ReflectionMethod($this->command, 'getBinaryName');

        $this->assertSame(self::BIN, $method->invoke($this->command));
    }
}
