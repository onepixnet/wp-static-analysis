<?php

namespace Onepix\WpStaticAnalysis\Tests\Cli\PHPCS;

use Onepix\WpStaticAnalysis\Cli\PHPCS\AbstractCommand;
use Onepix\WpStaticAnalysis\Cli\PHPCS\PhpcbfCommand;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;

#[CoversClass(PhpcbfCommand::class)]
class PhpcbfCommandTest extends TestCase
{
    private const BIN = 'phpcbf';

    private AbstractCommand $command;

    protected function setUp(): void
    {
        parent::setUp();

        $this->command = new PhpcbfCommand();
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testGetBinaryName(): void
    {
        $method = new ReflectionMethod($this->command, 'getBinaryName');

        $this->assertSame(self::BIN, $method->invoke($this->command));
    }
}
