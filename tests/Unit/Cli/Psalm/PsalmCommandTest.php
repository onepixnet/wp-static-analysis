<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Tests\Unit\Cli\Psalm;

use Onepix\WpStaticAnalysis\Cli\Psalm\PsalmCommand;
use Onepix\WpStaticAnalysis\Tests\Util\ExposeProtectedMethods;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * Test class for PsalmCommand.
 */
#[CoversClass(PsalmCommand::class)]
final class PsalmCommandTest extends TestCase
{
    use ExposeProtectedMethods;

    private PsalmCommand $command;

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->command = new PsalmCommand();
    }

    /**
     * Tests the getBinaryName method
     *
     * @return void
     * @throws ReflectionException
     */
    public function testGetBinaryName(): void
    {
        $this->assertSame('psalm', $this->callProtectedMethod($this->command, 'getBinaryName'));
    }
}
