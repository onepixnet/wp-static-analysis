<?php

namespace Onepix\WpStaticAnalysis\Tests\Unit\Cli\PHPCS;

use Onepix\WpStaticAnalysis\Cli\Command\AbstractCommand;
use Onepix\WpStaticAnalysis\Cli\PHPCS\PhpcbfCommand;
use Onepix\WpStaticAnalysis\Tests\Util\ExposeProtectedMethods;
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
    use ExposeProtectedMethods;

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
        $this->assertSame('phpcbf', $this->callProtectedMethod($this->command, 'getBinaryName'));
    }
}
