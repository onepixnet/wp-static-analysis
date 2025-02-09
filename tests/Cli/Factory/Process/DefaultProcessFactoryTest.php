<?php
declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Tests\Cli\Factory\Process;

use Onepix\WpStaticAnalysis\Cli\Factory\Process\DefaultProcessFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DefaultProcessFactory::class)]
class DefaultProcessFactoryTest extends TestCase
{
    public function testCreatesProcessWithCorrectConfiguration(): void
    {
        $factory = new DefaultProcessFactory();
        $process = $factory->create(['echo', 'test']);

        $this->assertEquals("'echo' 'test'", $process->getCommandLine());
    }
}
