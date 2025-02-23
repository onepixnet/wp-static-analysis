<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Cli\Psalm;

use Onepix\WpStaticAnalysis\Cli\Command\AbstractCommand;
use Onepix\WpStaticAnalysis\Cli\ConfigLocator\ConfigLocatorInterface;
use Onepix\WpStaticAnalysis\Cli\Psalm\PsalmConfigLocator;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractPsalmCommand extends AbstractCommand
{
    protected const PSALM_ARGUMENT = 'options';
    protected const CONFIG_OPTION = 'config';

    /** @var ConfigLocatorInterface Responsible for searching for configs */
    protected ConfigLocatorInterface $configLocator;

    /**
     * @inheritDoc
     * @throws LogicException
     */
    public function __construct(
        ?string $name = null
    ) {
        parent::__construct($name);

        $this->configLocator = new PsalmConfigLocator();
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
                self::PSALM_ARGUMENT,
                InputArgument::IS_ARRAY,
                'Write all Psalm-related arguments with a space in them'
            )
            ->addOption(
                self::CONFIG_OPTION,
                null,
                InputOption::VALUE_REQUIRED,
                'Path to custom psalm.xml relative to the project root'
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
        $args = $input->getArgument(self::PSALM_ARGUMENT);
        /** @var string|null $customConfig */
        $customConfig = $input->getOption(self::CONFIG_OPTION);
        $configPath = $this->configLocator->locate($customConfig);

        $binaryPath = $this->findBinary();
        $command = [
            $binaryPath,
            ...(is_array($args) ? $args : []),
            '--config=' . $configPath,
        ];

        $process = $this->processFactory->create($command);

        $process->run(function (string $type, string $buffer) use ($output) {
            $output->write($buffer);
        });

        return $process->isSuccessful()
            ? Command::SUCCESS
            : Command::FAILURE;
    }
}
