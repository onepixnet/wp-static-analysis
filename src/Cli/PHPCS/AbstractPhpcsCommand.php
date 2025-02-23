<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Cli\PHPCS;

use Onepix\WpStaticAnalysis\Cli\Command\AbstractCommand;
use Onepix\WpStaticAnalysis\Cli\ConfigLocator\ConfigLocatorInterface;
use Onepix\WpStaticAnalysis\Cli\PHPCS\StandardLocator;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractPhpcsCommand extends AbstractCommand
{
    protected const PHPCS_ARGUMENT = 'options';
    protected const STANDARD_OPTION = 'standard';

    /** @var ConfigLocatorInterface Responsible for finding standard files */
    protected ConfigLocatorInterface $standardLocator;

    public function __construct(?string $name = null)
    {
        parent::__construct($name);

        $this->standardLocator = new StandardLocator();
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
     * Set custom standard locator
     *
     * @param ConfigLocatorInterface $standardLocator
     */
    public function setStandardLocator(ConfigLocatorInterface $standardLocator): void
    {
        $this->standardLocator = $standardLocator;
    }
}
