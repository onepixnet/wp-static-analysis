<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Tests\Unit\Cli\PHPCS;

use Onepix\WpStaticAnalysis\Cli\PHPCS\StandardLocator;
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
class StandardLocatorTest extends TestCase
{
    /**
     * @var StandardLocator
     */
    private StandardLocator $locator;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->locator = new StandardLocator();
    }

    /**
     * Tests locating a custom standard
     *
     * @return void
     */
    public function testCustomRuleset(): void
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
     * Tests the scenario where the custom standard does not exist
     *
     * @return void
     */
    public function testCustomRulesetNotExist(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Custom standard not found: ./folder/missing.xml');

        $this->locator->locate('./folder/missing.xml');
    }

    /**
     * Tests locating the project standard
     *
     * @return void
     */
    public function testProjectRuleset(): void
    {
        $root = vfsStream::setup('project', null, [
            '.config' => [
                'phpcs.xml' => ''
            ]
        ]);
        $this->locator->setBasePath($root->url());
        $result = $this->locator->locate();

        $expectedPath = $root->url() . '/.config/phpcs.xml';
        $this->assertEquals($expectedPath, $result);
    }

    /**
     * Tests the default standard
     *
     * @return void
     */
    public function testDefaultStandard(): void
    {
        $result = $this->locator->locate();

        $this->assertEquals('WpOnepixStandard', $result);
    }

    /**
     * Tests getting the absolute path.
     *
     * @param string $basePath The base path.
     * @param string $relativePath The relative path.
     * @param string $expected The expected absolute path.
     * @return void
     * @throws ReflectionException
     * @dataProvider absolutePathDataProvider
     */
    public function testGetAbsolutePath(string $basePath, string $relativePath, string $expected): void
    {
        $this->locator->setBasePath($basePath);

        $reflection = new ReflectionClass(StandardLocator::class);
        $method = $reflection->getMethod('getAbsolutePath');

        $result = $method->invoke($this->locator, $relativePath);
        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for absolute path tests
     *
     * @return array<string, array{string, string, string}>
     */
    public static function absolutePathDataProvider(): array
    {
        return [
            'simple path' => [
                '/var/www',
                '.config/phpcs.xml',
                '/var/www/.config/phpcs.xml'
            ],
            'simple path 2' => [
                '/var/www',
                '.config/.phpcs.xml',
                '/var/www/.config/.phpcs.xml'
            ],
            'simple path 3' => [
                '/var/www',
                '.config/.phpcs.xml.dist',
                '/var/www/.config/.phpcs.xml.dist'
            ],
            'simple path 4' => [
                '/var/www',
                '.config/phpcs.xml.dist',
                '/var/www/.config/phpcs.xml.dist'
            ],
            'path with leading slash' => [
                '/var/www',
                '/.config/phpcs.xml',
                '/var/www/.config/phpcs.xml'
            ],
            'path with multiple slashes' => [
                '/var/www',
                '//.config///phpcs.xml',
                '/var/www/.config///phpcs.xml'
            ],
            'empty relative path' => [
                '/var/www',
                '',
                '/var/www/'
            ]
        ];
    }
}
