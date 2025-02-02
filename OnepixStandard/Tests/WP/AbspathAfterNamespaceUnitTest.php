<?php
declare(strict_types=1);

namespace OnepixStandard\Tests\WP;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * @covers \OnepixStandard\Sniffs\WP\AbspathAfterNamespaceSniff
 */
class AbspathAfterNamespaceUnitTest extends AbstractSniffUnitTest
{
    public function getWarningList(): array
    {
        return [];
    }

    protected function getErrorList(string $testFile = ''): array
    {
        return match ($testFile) {
            'AbspathAfterNamespaceUnitTest.1.inc', 'AbspathAfterNamespaceUnitTest.3.inc', => [
                3 => 1
            ],
            default => [],
        };
    }
}