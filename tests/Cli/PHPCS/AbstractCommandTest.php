<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Tests\Cli\PHPCS;

use Onepix\WpStaticAnalysis\Cli\PHPCS\AbstractCommand;
use Onepix\WpStaticAnalysis\Cli\PHPCS\RulesetLocator;
use Onepix\WpStaticAnalysis\Tests\Cli\Factory\Process\MockProcessFactory;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Test class for AbstractCommand.
 */
#[CoversClass(AbstractCommand::class)]
class AbstractCommandTest extends TestCase
{
    private const FAKE_BIN = AbstractCommandImplementation::FAKE_BIN;
    private const MOCK_BINARY_PATH = '/usr/local/bin/test-binary';

    private string $fakeBinDir;
    private AbstractCommand $command;
    private AbstractCommand|MockObject $commandMock;
    private Process|MockObject $processMock;
    private InputInterface|MockObject $inputMock;
    private OutputInterface|MockObject $outputMock;

    /**
     * @return void
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->command = new AbstractCommandImplementation();
        $this->inputMock = $this->createMock(InputInterface::class);
        $this->outputMock = $this->createMock(OutputInterface::class);
        $this->processMock = $this->createMock(Process::class);

        $rulesetLocatorMock = $this->createMock(RulesetLocator::class);
        $rulesetLocatorMock->method('locate')->willReturn('/path/to/ruleset.xml');

        $this->commandMock = $this->getMockBuilder(AbstractCommandImplementation::class)
            ->onlyMethods(['findBinary'])
            ->getMock();
        $this->commandMock->expects($this->any())
            ->method('findBinary')
            ->willReturn(self::MOCK_BINARY_PATH);
        $this->commandMock->setRulesetLocator($rulesetLocatorMock);
        $this->commandMock->setProcessFactory(new MockProcessFactory($this->processMock));
    }

    /**
     * Prepares a temporary directory for testing
     *
     * @return void
     */
    private function prepareTemp(): void
    {
        $this->fakeBinDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . str_replace('\\', '_', static::class);
        if (!file_exists($this->fakeBinDir)) {
            mkdir($this->fakeBinDir);
        }
        touch($this->fakeBinDir . DIRECTORY_SEPARATOR . self::FAKE_BIN);
        chmod($this->fakeBinDir . DIRECTORY_SEPARATOR . self::FAKE_BIN, 0755);
    }

    /**
     * Cleans up the temporary directory after testing
     *
     * @return void
     */
    private function cleanTemp(): void
    {
        if (file_exists($this->fakeBinDir)) {
            array_map(
                fn (string $file) =>
                is_dir($file) ? rmdir($file) : unlink($file),
                glob($this->fakeBinDir . '/' . '*')
            );
            rmdir($this->fakeBinDir);
        }
    }

    /**
     * Tests the successful execution of the command
     *
     * @return void
     * @throws ReflectionException
     * @throws Exception
     */
    public function testExecuteSuccessful(): void
    {
        $this->processMock->expects($this->once())
            ->method('run')
            ->willReturnCallback(function ($callback) {
                $callback('stdout', 'Test output');
                return 0;
            });
        $this->processMock->method('isSuccessful')->willReturn(true);

        $this->outputMock->expects($this->once())
            ->method('write')
            ->with('Test output');

        $reflectionClass = new ReflectionClass($this->commandMock);
        $executeMethod = $reflectionClass->getMethod('execute');

        $result = $executeMethod->invoke($this->commandMock, $this->inputMock, $this->outputMock);

        $this->assertEquals(0, $result);
    }

    /**
     * Tests the failure execution of the command
     *
     * @return void
     * @throws ReflectionException
     * @throws Exception
     */
    public function testExecuteFailure(): void
    {
        $this->processMock->method('run');
        $this->processMock->method('isSuccessful')->willReturn(false);

        $reflectionClass = new ReflectionClass($this->commandMock);
        $executeMethod = $reflectionClass->getMethod('execute');

        $result = $executeMethod->invoke($this->commandMock, $this->inputMock, $this->outputMock);

        $this->assertEquals(1, $result);
    }

    /**
     * Tests the getBinaryName method
     *
     * @return void
     * @throws ReflectionException
     */
    public function testGetBinaryName(): void
    {
        $method = new ReflectionMethod($this->command, 'getBinaryName');

        $this->assertSame(self::FAKE_BIN, $method->invoke($this->command));
    }

    /**
     * Tests finding the binary in the vendor directory
     *
     * @return void
     * @throws ReflectionException
     */
    public function testFindBinaryInVendor(): void
    {
        $root = vfsStream::setup('project', null, [
            'vendor' => [
                'bin' => [
                    self::FAKE_BIN => ''
                ]
            ]
        ]);
        $this->command->setBasePath($root->url());

        $method = new ReflectionMethod($this->command, 'findBinary');
        $binaryPath = $method->invoke($this->command);

        $this->assertSame($root->url() . '/vendor/bin/' . self::FAKE_BIN, $binaryPath);
    }

    /**
     * Tests finding the binary in the global PATH
     *
     * @return void
     * @throws ReflectionException
     */
    public function testFindBinaryGlobal(): void
    {
        $this->prepareTemp();
        $oldPath = getenv('PATH');
        putenv("PATH=" . $this->fakeBinDir . PATH_SEPARATOR . getenv('PATH'));

        $method = new ReflectionMethod($this->command, 'findBinary');
        $binaryPath = $method->invoke($this->command);

        $this->assertSame($this->fakeBinDir . DIRECTORY_SEPARATOR . self::FAKE_BIN, $binaryPath);

        putenv('PATH=' . $oldPath);
        $this->cleanTemp();
    }

    /**
     * Tests the scenario where the binary does not exist
     *
     * @return void
     * @throws ReflectionException
     */
    public function testFindBinaryNotExist(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(self::FAKE_BIN . ' not found. Install it via Composer.');

        $method = new ReflectionMethod($this->command, 'findBinary');
        $method->invoke($this->command);
    }
}
