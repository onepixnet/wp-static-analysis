<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Tests\Unit\Cli\ConfigLocator;

use Onepix\WpStaticAnalysis\Cli\ConfigLocator\AbstractConfigLocator;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Test class for AbstractConfigLocator.
 */
#[CoversClass(AbstractConfigLocator::class)]
final class AbstractConfigLocatorTest extends TestCase
{
    /** @var TestableAbstractConfigLocator $locator */
    private TestableAbstractConfigLocator $locator;

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->locator = new TestableAbstractConfigLocator();
    }

    /**
     * Tests locating a custom config
     *
     * @return void
     */
    public function testCustomConfig(): void
    {
        $root = vfsStream::setup('project', null, [
            'folder' => [
                'custom.xml' => ''
            ]
        ]);
        $this->locator->setBasePath($root->url());
        $result = $this->locator->locate('folder/custom.xml');

        $expectedPath = $root->url() . '/folder/custom.xml';
        $this->assertEquals($expectedPath, $result);
    }

    /**
     * Tests the scenario where the custom config does not exist
     *
     * @return void
     */
    public function testCustomConfigNotExist(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The specified config was not found: ./folder/missing.xml');

        $this->locator->locate('./folder/missing.xml');
    }

    /**
     * Tests locating the project config
     *
     * @return void
     */
    public function testProjectConfig(): void
    {
        $root = vfsStream::setup('project', null, [
            '.config' => [
                'test.xml' => ''
            ]
        ]);
        $this->locator->setBasePath($root->url());
        $result = $this->locator->locate();

        $expectedPath = $root->url() . '/.config/test.xml';
        $this->assertEquals($expectedPath, $result);
    }

    /**
     * Tests the default config
     *
     * @return void
     */
    public function testDefaultConfig(): void
    {
        $result = $this->locator->locate();

        $this->assertEquals('defaultPath', $result);
    }
}
