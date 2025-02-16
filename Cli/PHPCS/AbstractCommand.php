<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Cli\PHPCS;

use Onepix\WpStaticAnalysis\Cli\Factory\Process\DefaultProcessFactory;
use Onepix\WpStaticAnalysis\Cli\Factory\Process\ProcessFactoryInterface;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Base command for PHP CodeSniffer related commands
 */
abstract class AbstractCommand extends Command
{
    protected const PHPCS_ARGUMENT = 'options';
    protected const STANDARD_OPTION = 'standard';

    /** @var string|null Base path for file resolution */
    private ?string $basePath;

    /** @var StandardLocator Responsible for finding standard files */
    protected StandardLocator $standardLocator;

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
     */
    public function __construct(
        ?string $name = null
    ) {
        parent::__construct($name);

        $this->standardLocator = new StandardLocator();
        $this->processFactory = new DefaultProcessFactory();
        $this->basePath = getcwd();
    }

    /**
     * @inheritDoc
     */
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
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $args = $input->getArgument(self::PHPCS_ARGUMENT);
        $customStandardFile = $input->getOption(self::STANDARD_OPTION);
        $standardPath = $this->standardLocator->locate($customStandardFile);

        $binaryPath = $this->findBinary();
        $command = [
            $binaryPath,
            ...(is_array($args) ? $args : []),
            '--standard=' . $standardPath,
        ];

        $process = $this->processFactory->create($command);

        $process->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        return $process->isSuccessful()
            ? Command::SUCCESS
            : Command::FAILURE;
    }

    /**
     * Set base path for file resolution
     *
     * @param string|null $basePath
     */
    public function setBasePath(?string $basePath): void
    {
        $this->basePath = $basePath;
    }

    /**
     * Set custom standard locator
     *
     * @param StandardLocator $standardLocator
     */
    public function setStandardLocator(StandardLocator $standardLocator): void
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
     */
    protected function findBinary(): string
    {
        $binaryName = $this->getBinaryName();

        $localBinary = $this->basePath . "/vendor/bin/{$binaryName}";
        if (file_exists($localBinary)) {
            return $localBinary;
        }

        $globalBinary = shell_exec("which {$binaryName}");
        if ($globalBinary !== null) {
            return trim($globalBinary);
        }

        throw new RuntimeException(
            "{$binaryName} not found. Install it via Composer."
        );
    }
}
