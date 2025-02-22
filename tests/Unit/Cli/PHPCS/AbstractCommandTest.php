<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Tests\Unit\Cli\PHPCS;

use Onepix\WpStaticAnalysis\Cli\PHPCS\AbstractCommand;
use Onepix\WpStaticAnalysis\Cli\PHPCS\StandardLocatorInterface;
use Onepix\WpStaticAnalysis\Tests\Unit\Cli\Factory\Process\MockProcessFactory;
use Onepix\WpStaticAnalysis\Tests\Util\Filesystem;
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
final class AbstractCommandTest extends TestCase
{
    private const FAKE_BIN = AbstractCommandImplementation::FAKE_BIN;
    private const MOCK_BINARY_PATH = '/usr/local/bin/test-binary';

    private string $fakeBinDir;
    private AbstractCommand $command;
    /** @var AbstractCommand&MockObject */
    private $commandMock;

    /** @var Process&MockObject */
    private $processMock;
    /** @var InputInterface&MockObject */
    private $inputMock;
    /** @var OutputInterface&MockObject */
    private $outputMock;

    /**
     * @return void
     * @throws Exception
     */
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->command = new AbstractCommandImplementation();
        $this->inputMock = $this->createMock(InputInterface::class);
        $this->outputMock = $this->createMock(OutputInterface::class);
        $this->processMock = $this->createMock(Process::class);

        $standardLocatorMock = $this->createMock(StandardLocatorInterface::class);
        $standardLocatorMock->method('locate')->willReturn('/path/to/phpcs.xml');

        $this->commandMock = $this->getMockBuilder(AbstractCommandImplementation::class)
            ->onlyMethods(['findBinary'])
            ->getMock();
        $this->commandMock->expects($this->any())
            ->method('findBinary')
            ->willReturn(self::MOCK_BINARY_PATH);
        $this->commandMock->setStandardLocator($standardLocatorMock);
        $this->commandMock->setProcessFactory(new MockProcessFactory($this->processMock));
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
            ->willReturnCallback(function (callable $callback) {
                $callback(Process::OUT, 'Test output');
                return 0;
            });
        $this->processMock->method('isSuccessful')->willReturn(true);

        $this->outputMock->expects($this->once())
            ->method('write')
            ->with('Test output');

        $reflectionClass = new ReflectionClass($this->commandMock);
        $executeMethod = $reflectionClass->getMethod('execute');

        /** @var int $result */
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

        /** @var int $result */
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
        $oldPath = (string) getenv('PATH');
        putenv("PATH=" . $this->fakeBinDir . PATH_SEPARATOR . $oldPath);

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
        Filesystem::deleteFolder($this->fakeBinDir);
    }
}
