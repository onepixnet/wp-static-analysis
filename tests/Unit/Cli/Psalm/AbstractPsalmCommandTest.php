<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Tests\Unit\Cli\Psalm;

use Onepix\WpStaticAnalysis\Cli\Psalm\AbstractPsalmCommand;
use Onepix\WpStaticAnalysis\Cli\Psalm\PsalmConfigLocator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Test class for AbstractPsalmCommand.
 */
#[CoversClass(AbstractPsalmCommand::class)]
final class AbstractPsalmCommandTest extends TestCase
{
    private TestableAbstractPsalmCommand $command;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->command = new TestableAbstractPsalmCommand();
    }

    public function testConstructorInitializes(): void
    {
        $this->assertInstanceOf(PsalmConfigLocator::class, $this->command->getConfigLocator());
    }

    public function testConfigOptionName(): void
    {
        $this->assertSame('config', $this->command->getConfigOptionName());
    }

    public function testToolConfigFlag(): void
    {
        $this->assertSame('--config=', $this->command->getToolConfigFlag());
    }

    public function testToolArgumentsDescription(): void
    {
        $description = $this->command->getToolArgumentsDescription();

        $this->assertStringContainsString('Psalm', $description);
        $this->assertStringContainsString('arguments', $description);
    }

    public function testConfigOptionDescription(): void
    {
        $this->assertStringContainsString(
            'psalm.xml',
            $this->command->getConfigOptionDescription()
        );
    }
}
