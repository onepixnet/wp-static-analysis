<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Tests\Cli\PHPCS;

use Onepix\WpStaticAnalysis\Cli\PHPCS\AbstractCommand;
use Onepix\WpStaticAnalysis\Cli\PHPCS\PhpcsCommand;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;

/**
 * Test class for PhpcsCommand.
 */
#[CoversClass(PhpcsCommand::class)]
class PhpcsCommandTest extends TestCase
{
    private const BIN = 'phpcs';

    private AbstractCommand $command;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->command = new PhpcsCommand();
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
