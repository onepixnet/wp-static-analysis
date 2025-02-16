<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Tests\Integration;

use Onepix\WpStaticAnalysis\Tests\Util\Filesystem;
use PHPUnit\Framework\TestCase;

class CliTest extends TestCase
{
    private static string $projectRoot;
    private static string $binPath;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$projectRoot = realpath(__DIR__ . '/Project');

        exec("composer install -d " . self::$projectRoot . " --no-interaction", $output, $code);
        self::assertEquals(0, $code, 'Project dependencies install failed');

        self::$binPath = self::$projectRoot . '/vendor/bin/wp-static-analysis';
    }

    public function testExecuteSuccessful(): void
    {
        exec("cd " . self::$projectRoot . " && " . self::$binPath . " phpcs -- src/correct", $output, $status);
        $this->assertEquals(0, $status);
        $this->assertStringContainsString('(100%)', implode("\n", $output));
    }

    public function testExecuteFailure(): void
    {
        exec("cd " . self::$projectRoot . " && " . self::$binPath . " phpcs -- src/incorrect", $output, $status);
        $this->assertEquals(1, $status);
        $this->assertStringContainsString('ERROR', implode("\n", $output));
    }

    public static function tearDownAfterClass(): void
    {
        Filesystem::deleteFolder(self::$projectRoot . '/vendor');
        parent::tearDownAfterClass();
    }
}
