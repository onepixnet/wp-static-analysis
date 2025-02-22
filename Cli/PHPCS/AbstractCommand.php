<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Cli\PHPCS;

use Onepix\WpStaticAnalysis\Cli\Factory\Process\DefaultProcessFactory;
use Onepix\WpStaticAnalysis\Cli\Factory\Process\ProcessFactoryInterface;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Base command for PHP CodeSniffer related commands
 */
abstract class AbstractCommand extends Command
{
    protected const PHPCS_ARGUMENT = 'options';
    protected const STANDARD_OPTION = 'standard';

    /** @var string Base path for file resolution */
    private string $basePath;

    /** @var StandardLocatorInterface Responsible for finding standard files */
    protected StandardLocatorInterface $standardLocator;

    /** @var ProcessFactoryInterface Factory for creating processes */
    private ProcessFactoryInterface $processFactory;

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

        $this->standardLocator = new StandardLocator();
        $this->processFactory = new DefaultProcessFactory();

        $cwd = getcwd();
        $this->basePath = $cwd ?: '';
    }

    /**
     * @inheritDoc
     * @throws InvalidArgumentException
     */
    #[\Override]
    protected function configure(): void
    {
        $this
            ->addArgument(
                self::PHPCS_ARGUMENT,
                InputArgument::IS_ARRAY,
                'Spell out any arguments related to PHPCS with a space.'
            )
            ->addOption(
                self::STANDARD_OPTION,
                null,
                InputOption::VALUE_REQUIRED,
                'Path to custom phpcs.xml relative to project root'
            );
    }

    /**
     * @inheritDoc
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws LogicException
     * @throws \Symfony\Component\Process\Exception\LogicException
     */
    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var array<string>|null $args */
        $args = $input->getArgument(self::PHPCS_ARGUMENT);
        /** @var string|null $customStandardFile */
        $customStandardFile = $input->getOption(self::STANDARD_OPTION);
        $standardPath = $this->standardLocator->locate($customStandardFile);

        $binaryPath = $this->findBinary();
        $command = [
            $binaryPath,
            ...(is_array($args) ? $args : []),
            '--standard=' . $standardPath,
        ];

        $process = $this->processFactory->create($command);

        $process->run(function (string $type, string $buffer) use ($output) {
            $output->write($buffer);
        });

        return $process->isSuccessful()
            ? Command::SUCCESS
            : Command::FAILURE;
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
     * Set custom standard locator
     *
     * @param StandardLocatorInterface $standardLocator
     */
    public function setStandardLocator(StandardLocatorInterface $standardLocator): void
    {
        $this->standardLocator = $standardLocator;
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
