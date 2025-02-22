<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Cli\PHPCS;

interface StandardLocatorInterface
{
    public function locate(?string $customStandard = null): string;
}
