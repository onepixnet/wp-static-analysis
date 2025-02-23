<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Cli\Command;

use Onepix\WpStaticAnalysis\Cli\ConfigLocator\ConfigLocatorInterface;
use RuntimeException;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use LogicException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractToolCommand extends AbstractCommand
{
    /** @var ConfigLocatorInterface Responsible for finding config files */
    protected ConfigLocatorInterface $configLocator;

    /**
     * Flag for the config path in the tool
     *
     * @example '--standard='
     * @example '--config='
     * @return string
     */
    abstract protected function getToolConfigFlag(): string;

    /**
     * Description for the arguments of the tool
     *
     * @return string
     */
    abstract protected function getConfigOptionDescription(): string;

    /**
     * Description for the configure option
     *
     * @return string
     */
    abstract protected function getToolArgumentsDescription(): string;

    /**
     * Argument name to retrieve options for the tool
     *
     * @return string
     *
     * @psalm-return 'tool_options'
     */
    protected function getToolArgumentsName(): string
    {
        return 'tool_options';
    }

    /**
     * The name of the option to which the path to the custom configuration is passed
     *
     * @return string
     */
    protected function getConfigOptionName(): string
    {
        return 'config';
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
                $this->getToolArgumentsName(),
                InputArgument::IS_ARRAY,
                $this->getToolArgumentsDescription()
            )
            ->addOption(
                $this->getConfigOptionName(),
                null,
                InputOption::VALUE_REQUIRED,
                $this->getConfigOptionDescription()
            );
    }

    /**
     * @inheritDoc
     * @throws RuntimeException
     * @throws LogicException
     * @throws \Symfony\Component\Process\Exception\LogicException
     * @throws InvalidArgumentException
     */
    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string|null $customConfigFile */
        $customConfigFile = $input->getOption($this->getConfigOptionName());
        $configPath = $this->configLocator->locate($customConfigFile);

        /** @var string[] $command */
        $command = array_merge(
            [$this->findBinary()],
            $this->prepareToolOptions($input),
            [$this->getToolConfigFlag() . $configPath]
        );

        $process = $this->processFactory->create($command);
        $process->run(function (string $type, string $buffer) use ($output) {
            $output->write($buffer);
        });

        return $process->isSuccessful() ? self::SUCCESS : self::FAILURE;
    }

    /**
     * Getting arguments to the tool
     *
     * @param InputInterface $input
     * @return array
     * @throws InvalidArgumentException
     */
    protected function prepareToolOptions(InputInterface $input): array
    {
        return (array) $input->getArgument($this->getToolArgumentsName());
    }

    /**
     * @param ConfigLocatorInterface $configLocator
     */
    public function setConfigLocator(ConfigLocatorInterface $configLocator): void
    {
        $this->configLocator = $configLocator;
    }
}
