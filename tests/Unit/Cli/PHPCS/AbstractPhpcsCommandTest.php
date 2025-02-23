<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Tests\Unit\Cli\PHPCS;

use Onepix\WpStaticAnalysis\Cli\PHPCS\AbstractPhpcsCommand;
use Onepix\WpStaticAnalysis\Cli\PHPCS\StandardLocator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Test class for AbstractPhpcsCommand.
 */
#[CoversClass(AbstractPhpcsCommand::class)]
final class AbstractPhpcsCommandTest extends TestCase
{
    private TestableAbstractPhpcsCommand $command;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->command = new TestableAbstractPhpcsCommand();
    }

    public function testConstructorInitializes(): void
    {
        $this->assertInstanceOf(StandardLocator::class, $this->command->getConfigLocator());
    }

    public function testConfigOptionName(): void
    {
        $this->assertSame('ruleset', $this->command->getConfigOptionName());
    }

    public function testToolConfigFlag(): void
    {
        $this->assertSame('--standard=', $this->command->getToolConfigFlag());
    }

    public function testToolArgumentsDescription(): void
    {
        $description = $this->command->getToolArgumentsDescription();

        $this->assertStringContainsString('PHPCS', $description);
        $this->assertStringContainsString('arguments', $description);
    }

    public function testConfigOptionDescription(): void
    {
        $this->assertStringContainsString(
            'phpcs.xml',
            $this->command->getConfigOptionDescription()
        );
    }
}
