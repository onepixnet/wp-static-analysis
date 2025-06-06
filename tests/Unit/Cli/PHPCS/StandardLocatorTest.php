<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Tests\Unit\Cli\PHPCS;

use Onepix\WpStaticAnalysis\Cli\PHPCS\StandardLocator;
use Onepix\WpStaticAnalysis\Tests\Util\ExposeProtectedMethods;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use RuntimeException;

/**
 * Test class for StandardLocator.
 */
#[CoversClass(StandardLocator::class)]
final class StandardLocatorTest extends TestCase
{
    use ExposeProtectedMethods;

    private StandardLocator $locator;

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->locator = new StandardLocator();
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testDefaultConfigPath()
    {
        /** @var string $configPath */
        $configPath = $this->callProtectedMethod($this->locator, 'getDefaultConfigPath');
        $this->assertStringContainsString('Standard', $configPath);
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testGetProjectConfigPaths()
    {
        $this->assertIsArray(
            $this->callProtectedMethod($this->locator, 'getProjectConfigPaths')
        );
    }
}
