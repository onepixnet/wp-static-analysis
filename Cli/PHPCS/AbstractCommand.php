<?php
declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Cli\PHPCS;

use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

abstract class AbstractCommand extends Command
{
    protected const PHPCS_ARGUMENT = 'options';
    protected const RULESET_OPTION = 'ruleset';

    protected RulesetLocator $rulesetLocator;

    /**
     * @return string
     */
    abstract protected function getBinaryName(): string;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->rulesetLocator = new RulesetLocator();
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
            ...$args,
            '--standard=' . $rulesetPath,
        ];

        $process = new Process($command);
        $process->setTimeout(300);

        $process->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        return $process->isSuccessful()
            ? Command::SUCCESS
            : Command::FAILURE;
    }

    /**
     * @return string
     */
    protected function findBinary(): string
    {
        $binaryName = $this->getBinaryName();

        $localBinary = getcwd() . "/vendor/bin/{$binaryName}";
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