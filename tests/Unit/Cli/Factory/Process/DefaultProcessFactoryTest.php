<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Tests\Unit\Cli\Factory\Process;

use Onepix\WpStaticAnalysis\Cli\Factory\Process\DefaultProcessFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/** Tests default process factory configuration */
#[CoversClass(DefaultProcessFactory::class)]
final class DefaultProcessFactoryTest extends TestCase
{
    /** Verify factory creates processes with expected command line formatting */
    public function testCreatesProcessWithCorrectConfiguration(): void
    {
        $factory = new DefaultProcessFactory();
        $process = $factory->create(['echo', 'test']);

        $this->assertEquals("'echo' 'test'", $process->getCommandLine());
    }
}
