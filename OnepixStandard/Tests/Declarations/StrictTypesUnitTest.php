<?php
declare(strict_types=1);

namespace OnepixStandard\Tests\Declarations;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * @covers \OnepixStandard\Sniffs\Declarations\StrictTypesSniff
 */
class StrictTypesUnitTest extends AbstractSniffUnitTest
{
    public function getWarningList(): array
    {
        return [];
    }

    protected function getErrorList(): array
    {
        return [
            1 => 1
        ];
    }
}