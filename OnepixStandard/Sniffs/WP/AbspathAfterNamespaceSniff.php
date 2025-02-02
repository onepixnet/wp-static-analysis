<?php
declare(strict_types=1);

namespace OnepixStandard\Sniffs\WP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class AbspathAfterNamespaceSniff implements Sniff {
	private const REQUIRED_CONSTANT = "'ABSPATH'";
	private const EXPECTED_SEQUENCE = [
		T_STRING,    // defined
		T_OPEN_PARENTHESIS,
		T_CONSTANT_ENCAPSED_STRING, // 'ABSPATH'
		T_CLOSE_PARENTHESIS,
		T_BOOLEAN_OR,  // ||
		T_EXIT,     // exit
		T_OPEN_PARENTHESIS,
		T_CLOSE_PARENTHESIS,
		T_SEMICOLON
	];

	public function register(): array {
		return [ T_NAMESPACE ];
	}

	public function process(File $phpcsFile, $stackPtr): void
	{
		$namespaceEnd = $phpcsFile->findEndOfStatement($stackPtr);
		$nextToken = $phpcsFile->findNext(T_WHITESPACE, ($namespaceEnd + 1), null, true);

		if ($nextToken === false) {
			$phpcsFile->addError(
				'Missing ABSPATH check after namespace declaration',
				$stackPtr,
				'MissingAbspathCheck'
			);
			return;
		}

		if (!$this->isValidSequence($phpcsFile, $nextToken)) {
			$fix = $phpcsFile->addFixableError(
				'Missing or incorrect ABSPATH check after namespace declaration',
				$stackPtr,
				'IncorrectAbspathCheck'
			);

			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->addNewline($namespaceEnd);
				$phpcsFile->fixer->addContent($namespaceEnd, "\ndefined('ABSPATH') || exit();");
				$phpcsFile->fixer->endChangeset();
			}
		}
	}

	private function isValidSequence(File $phpcsFile, int $startPos): bool
	{
		$tokens = $phpcsFile->getTokens();
		$pos = $startPos;

		foreach (self::EXPECTED_SEQUENCE as $expected) {
			if (!isset($tokens[$pos])) {
				return false;
			}

			// Check the token type
			if (is_int($expected) && $tokens[$pos]['code'] !== $expected) {
				return false;
			}

			// Checking a constant
			if ($tokens[$pos]['code'] === T_CONSTANT_ENCAPSED_STRING) {
				if ($tokens[$pos]['content'] !== self::REQUIRED_CONSTANT) {
					return false;
				}
			}

			// Skip spaces
			$pos = $phpcsFile->findNext(T_WHITESPACE, $pos + 1, null, true);
			if ($pos === false) {
				return false;
			}
		}

		return true;
	}
}
