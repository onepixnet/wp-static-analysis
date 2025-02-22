<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Cli\Psalm;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;

final class PsalmCommand extends Command
{
    /**
     * @inheritDoc
     * @throws LogicException
     */
    public function __construct(
        ?string $name = null
    ) {
        parent::__construct($name);
    }
}
