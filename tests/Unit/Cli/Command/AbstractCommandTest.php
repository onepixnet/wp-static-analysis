<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Tests\Unit\Cli\Command;

use Onepix\WpStaticAnalysis\Cli\Command\AbstractCommand;
use Onepix\WpStaticAnalysis\Cli\Factory\Process\ProcessFactoryInterface;
use Onepix\WpStaticAnalysis\Tests\Util\Filesystem;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Test class for AbstractCommand.
 */
#[CoversClass(AbstractCommand::class)]
final class AbstractCommandTest extends TestCase
{
    private const FAKE_BIN = TestableAbstractCommand::BIN;

    private string $fakeBinDir;
    private TestableAbstractCommand $command;

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->command = new TestableAbstractCommand();
    }

    /**
     * Tests finding the binary in the vendor directory
     *
     * @return void
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

        $binaryPath = $this->command->publicFindBinary();
        $this->assertSame($root->url() . '/vendor/bin/' . self::FAKE_BIN, $binaryPath);
    }

    /**
     * Tests finding the binary in the global PATH
     *
     * @return void
     */
    public function testFindBinaryGlobal(): void
    {
        $this->prepareTemp();
        $oldPath = (string) getenv('PATH');
        putenv("PATH=" . $this->fakeBinDir . PATH_SEPARATOR . $oldPath);

        $binaryPath = $this->command->publicFindBinary();
        $this->assertSame($this->fakeBinDir . DIRECTORY_SEPARATOR . self::FAKE_BIN, $binaryPath);

        putenv('PATH=' . $oldPath);
        $this->cleanTemp();
    }

    /**
     * Tests the scenario where the binary does not exist
     *
     * @return void
     */
    public function testFindBinaryNotExist(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(self::FAKE_BIN . ' not found. Install it via Composer.');

        $this->command->publicFindBinary();
    }

    /**
     * Tests setting a custom process factory
     *
     * @return void
     * @throws Exception
     */
    public function testSetProcessFactory(): void
    {
        $processFactoryMock = $this->createMock(ProcessFactoryInterface::class);
        $this->command->setProcessFactory($processFactoryMock);

        $this->assertSame(
            $processFactoryMock,
            $this->command->getProcessFactory(),
            'Process factory should be updated'
        );
    }

    /**
     * Prepares a temporary directory for testing
     *
     * @return void
     */
    private function prepareTemp(): void
    {
        $this->fakeBinDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . str_replace(
            '\\',
            '_',
            AbstractCommandTest::class
        );

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
