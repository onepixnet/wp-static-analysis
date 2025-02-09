<?php
declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Cli\PHPCS;

use Onepix\WpStaticAnalysis\Cli\Factory\Process\DefaultProcessFactory;
use Onepix\WpStaticAnalysis\Cli\Factory\Process\ProcessFactoryInterface;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command
{
    protected const PHPCS_ARGUMENT = 'options';
    protected const RULESET_OPTION = 'ruleset';

    /**
     * @var string|null
     */
    private ?string $basePath;
    protected RulesetLocator $rulesetLocator;
    private ProcessFactoryInterface $processFactory;

    /**
     * @return string
     */
    abstract protected function getBinaryName(): string;

    public function __construct(
        ?string $name = null
    ) {
        parent::__construct($name);

        $this->rulesetLocator = new RulesetLocator();
        $this->processFactory = new DefaultProcessFactory();
        $this->basePath = getcwd();
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->addArgument(
                self::PHPCS_ARGUMENT,
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                'Spell out any arguments related to PHPCS with a space.'
            )
            ->addOption(
                self::RULESET_OPTION,
                null,
                InputOption::VALUE_REQUIRED,
                'Path to custom ruleset.xml relative to project root'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $args = $input->getArgument(self::PHPCS_ARGUMENT);
        $customRulesetFile = $input->getOption(self::RULESET_OPTION);
        $rulesetPath = $this->rulesetLocator->locate($customRulesetFile);

        $binaryPath = $this->findBinary();
        $command = [
            $binaryPath,
            ...(is_array($args) ? $args : []),
            '--standard=' . $rulesetPath,
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
     * @param string|null $basePath
     */
    public function setBasePath(?string $basePath): void
    {
        $this->basePath = $basePath;
    }

    /**
     * @param RulesetLocator $rulesetLocator
     */
    public function setRulesetLocator(RulesetLocator $rulesetLocator): void
    {
        $this->rulesetLocator = $rulesetLocator;
    }

    /**
     * @param ProcessFactoryInterface $processFactory
     */
    public function setProcessFactory(ProcessFactoryInterface $processFactory): void
    {
        $this->processFactory = $processFactory;
    }

    /**
     * @return string
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
