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

#[CoversClass(RulesetLocator::class)]
class RulesetLocatorTest extends TestCase
{
    /**
     * @var RulesetLocator
     */
    private RulesetLocator $locator;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->locator = new RulesetLocator();
    }

    /**
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
     * @return void
     */
    public function testCustomRulesetNotExist(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Custom ruleset not found: ./folder/missing.xml');

        $this->locator->locate('./folder/missing.xml');
    }

    /**
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
     * @return void
     */
    public function testDefaultStandard(): void
    {
        $result = $this->locator->locate();

        $this->assertEquals('WpOnepixStandard', $result);
    }

    /**
     * @dataProvider absolutePathDataProvider
     *
     * @param string $basePath
     * @param string $relativePath
     * @param string $expected
     * @return void
     * @throws ReflectionException
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
