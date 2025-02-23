<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Cli\Command;

use Onepix\WpStaticAnalysis\Cli\Factory\Process\DefaultProcessFactory;
use Onepix\WpStaticAnalysis\Cli\Factory\Process\ProcessFactoryInterface;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Process\Process;

/**
 * Base command for PHP CodeSniffer related commands
 */
abstract class AbstractCommand extends Command
{
    /** @var string Base path for file resolution */
    private string $basePath;

    /** @var ProcessFactoryInterface Factory for creating processes */
    protected ProcessFactoryInterface $processFactory;

    /**
     * Get the name of the PHPCS binary to execute
     *
     * @return string
     */
    abstract protected function getBinaryName(): string;

    /**
     * @inheritDoc
     * @throws LogicException
     */
    public function __construct(
        ?string $name = null
    ) {
        parent::__construct($name);

        $this->processFactory = new DefaultProcessFactory();

        $this->basePath = getcwd() ?: '';
    }

    /**
     * Set base path for file resolution
     *
     * @param string $basePath
     */
    public function setBasePath(string $basePath): void
    {
        $this->basePath = $basePath;
    }

    /**
     * Set custom process factory
     *
     * @param ProcessFactoryInterface $processFactory
     */
    public function setProcessFactory(ProcessFactoryInterface $processFactory): void
    {
        $this->processFactory = $processFactory;
    }

    /**
     * Find PHPCS binary in vendor or global path
     *
     * @return string
     *
     * @throws RuntimeException If binary is not found
     * @throws LogicException
     * @throws \Symfony\Component\Process\Exception\LogicException
     */
    protected function findBinary(): string
    {
        $binaryName = $this->getBinaryName();

        $localBinary = $this->basePath . "/vendor/bin/{$binaryName}";
        if (file_exists($localBinary)) {
            return $localBinary;
        }

        $env = getenv();
        $process = new Process(['which', $binaryName], null, $env);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException("{$binaryName} not found. Install it via Composer.");
        }

        return trim($process->getOutput());
    }
}
