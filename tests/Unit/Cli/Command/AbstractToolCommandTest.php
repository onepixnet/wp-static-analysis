<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Tests\Unit\Cli\Command;

use Onepix\WpStaticAnalysis\Cli\Command\AbstractToolCommand;
use Onepix\WpStaticAnalysis\Cli\ConfigLocator\ConfigLocatorInterface;
use Onepix\WpStaticAnalysis\Cli\Factory\Process\ProcessFactoryInterface;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Test class for AbstractCommand.
 */
#[CoversClass(AbstractToolCommand::class)]
final class AbstractToolCommandTest extends TestCase
{
    private const DEFAULT_CONFIG_PATH = '.config/test.xml';

    private TestableAbstractToolCommand $command;
    private MockObject&ConfigLocatorInterface $configLocatorMock;
    private MockObject&ProcessFactoryInterface $processFactoryMock;
    private MockObject&Process $processMock;

    /**
     * @return void
     * @throws Exception
     */
    #[\Override]
    protected function setUp(): void
    {
        $this->command = new TestableAbstractToolCommand();
        $this->processMock = $this->createMock(Process::class);
        $this->processFactoryMock = $this->createMock(ProcessFactoryInterface::class);
        $this->processFactoryMock
            ->method('create')
            ->willReturn($this->processMock);
        $this->configLocatorMock = $this->createMock(ConfigLocatorInterface::class);
        $this->configLocatorMock
            ->method('locate')
            ->willReturn(self::DEFAULT_CONFIG_PATH);

        $this->command->setConfigLocator($this->configLocatorMock);
        $this->command->setProcessFactory($this->processFactoryMock);

        $root = vfsStream::setup('project', null, [
            'vendor' => [
                'bin' => [
                    TestableAbstractToolCommand::BINARY_NAME => ''
                ]
            ]
        ]);
        $this->command->setBasePath($root->url());
    }

    public function testExecuteWithCustomConfigAndArguments(): void
    {
        $customConfig = 'custom.xml';
        $toolArguments = ['--verbose', '/path/to/file'];

        $this->processMock->method('run')->willReturnCallback(
            function (callable $callback) {
                $callback(Process::OUT, 'test output');
                return 0;
            }
        );
        $this->processMock->method('run')->willReturn(0);
        $this->processMock->method('isSuccessful')->willReturn(true);

        $input = new ArrayInput([
            'tool_options' => $toolArguments,
            '--config' => $customConfig,
        ], $this->command->getDefinition());

        $output = $this->createMock(OutputInterface::class);

        $result = $this->command->publicExecute($input, $output);

        $this->assertEquals(Command::SUCCESS, $result);
    }

    public function testExecuteReturnsFailureOnProcessError(): void
    {
        $input = new ArrayInput([], $this->command->getDefinition());
        $output = $this->createMock(OutputInterface::class);

        $this->configLocatorMock
            ->method('locate')
            ->willReturn(self::DEFAULT_CONFIG_PATH);

        $this->processMock->method('run')->willReturn(1);
        $this->processMock->method('isSuccessful')->willReturn(false);

        $result = $this->command->publicExecute($input, $output);

        $this->assertEquals(Command::FAILURE, $result);
    }
}
