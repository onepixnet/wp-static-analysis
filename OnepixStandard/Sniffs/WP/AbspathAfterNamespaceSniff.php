<?php
declare(strict_types=1);

namespace OnepixStandard\Sniffs\WP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class AbspathAfterNamespaceSniff implements Sniff
{
    private const REQUIRED_CONSTANT = "'ABSPATH'";
    private const EXPECTED_SEQUENCE = [
        // defined
        T_STRING,
        // (
        T_OPEN_PARENTHESIS,
        // 'ABSPATH'
        T_CONSTANT_ENCAPSED_STRING,
        // )
        T_CLOSE_PARENTHESIS,
        // ||
        T_BOOLEAN_OR,
        // exit
        T_EXIT,
        // ;
        T_SEMICOLON
    ];

    public function register(): array
    {
        return [T_NAMESPACE];
    }

    public function process(
        File $phpcsFile,
             $stackPtr
    ): void
    {
        $namespaceEnd = $phpcsFile->findEndOfStatement($stackPtr);

        $startPosition = $namespaceEnd + 1;
        $found = $this->findDefinedABSPATHSequence($phpcsFile, $startPosition);

        if (!$found) {
            $fix = $phpcsFile->addFixableError(
                'After the namespace there should be a line defined(\'ABSPATH\') || exit;',
                $namespaceEnd,
                'MissingABSPATHCheck'
            );

            if ($fix) {
                $this->applyFix($phpcsFile, $namespaceEnd);
            }
        }
    }

    private function findDefinedABSPATHSequence(
        File $phpcsFile,
        int  $position
    ): bool
    {
        $tokens = $phpcsFile->getTokens();
        $currentPosition = $position;

        foreach (self::EXPECTED_SEQUENCE as $expectedTokenCode) {
            // Skip spaces and comments
            $currentPosition = $phpcsFile->findNext(
                [T_WHITESPACE, T_COMMENT],
                $currentPosition,
                null,
                true
            );

            if ($currentPosition === false) {
                return false;
            }

            $currentToken = $tokens[$currentPosition];

            // Checking compliance with the token code
            if ($currentToken['code'] !== $expectedTokenCode) {
                return false;
            }

            // Additional content checks for specific tokens
            switch ($expectedTokenCode) {
                case T_STRING:
                    if (strtolower($currentToken['content']) !== 'defined') {
                        return false;
                    }
                    break;
                case T_CONSTANT_ENCAPSED_STRING:
                    if ($currentToken['content'] !== self::REQUIRED_CONSTANT) {
                        return false;
                    }
                    break;
            }

            $currentPosition++;
        }

        return true;
    }

    private function applyFix(File $phpcsFile, int $namespaceEnd): void
    {
        $phpcsFile->fixer->beginChangeset();

        $phpcsFile->fixer->addNewline($namespaceEnd);
        $phpcsFile->fixer->addNewline($namespaceEnd);
        $phpcsFile->fixer->addContent(
            $namespaceEnd,
            "defined('ABSPATH') || exit;"
        );

        $phpcsFile->fixer->endChangeset();
    }
}
