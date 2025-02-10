<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Tests\Cli\PHPCS;

use Onepix\WpStaticAnalysis\Cli\PHPCS\RulesetLocator;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use RuntimeException;

/**
 * Test class for RulesetLocator.
 */
#[CoversClass(RulesetLocator::class)]
class RulesetLocatorTest extends TestCase
{
    /**
     * @var RulesetLocator
     */
    private RulesetLocator $locator;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->locator = new RulesetLocator();
    }

    /**
     * Tests locating a custom ruleset
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
     * Tests the scenario where the custom ruleset does not exist
     *
     * @return void
     */
    public function testCustomRulesetNotExist(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Custom ruleset not found: ./folder/missing.xml');

        $this->locator->locate('./folder/missing.xml');
    }

    /**
     * Tests locating the project ruleset
     *
     * @return void
     */
    public function testProjectRuleset(): void
    {
        $root = vfsStream::setup('project', null, [
            'config' => [
                'ruleset.xml' => ''
            ]
        ]);
        $this->locator->setBasePath($root->url());
        $result = $this->locator->locate();

        $expectedPath = $root->url() . '/config/ruleset.xml';
        $this->assertEquals($expectedPath, $result);
    }

    /**
     * Tests the default standard ruleset
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

        $reflection = new ReflectionClass(RulesetLocator::class);
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
                'config/ruleset.xml',
                '/var/www/config/ruleset.xml'
            ],
            'path with leading slash' => [
                '/var/www',
                '/config/ruleset.xml',
                '/var/www/config/ruleset.xml'
            ],
            'path with multiple slashes' => [
                '/var/www',
                '//config///ruleset.xml',
                '/var/www/config///ruleset.xml'
            ],
            'empty relative path' => [
                '/var/www',
                '',
                '/var/www/'
            ]
        ];
    }
}
