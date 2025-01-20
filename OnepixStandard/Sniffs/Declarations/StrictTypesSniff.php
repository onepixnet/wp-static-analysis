<?php
declare(strict_types=1);

namespace OnepixStandard\Sniffs\Declarations;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class StrictTypesSniff implements Sniff {
	private const ERROR_MESSAGE = 'declare(strict_types=1); required';
	private const STRICT_TYPES_VALUE = '1';

	/**
	 * @return array<int> Array of tokens to listen for
	 */
	public function register(): array
	{
		return [T_OPEN_TAG];
	}

	/**
	 * @param File $phpcsFile The file being scanned
	 * @param int $stackPtr The position in the stack where the token was found
	 */
	public function process(File $phpcsFile, $stackPtr): void
	{
		// Check if PHP open tag is the first token in file
		if ($stackPtr !== 0) {
			return;
		}

		$tokens = $phpcsFile->getTokens();
		$declarePosition = $phpcsFile->findNext(T_DECLARE, $stackPtr);

		// If file doesn't have declare statement
		if ($declarePosition === false) {
			$this->addMissingDeclare($phpcsFile, $stackPtr);
			return;
		}

		$this->validateEmptyLines($phpcsFile, $stackPtr, $declarePosition);

		$declareTokens = $this->getDeclareTokens($phpcsFile, $declarePosition);
		if ($declareTokens === null) {
			return;
		}

		$this->validateStrictTypes($phpcsFile, $tokens, $declareTokens, $declarePosition);
	}

	/**
	 * Validates that there are no empty lines between PHP tag and declare
	 *
	 * @param File $phpcsFile The file being scanned
	 * @param int $stackPtr Position of PHP open tag
	 * @param int $declarePosition Position of declare statement
	 */
	private function validateEmptyLines(File $phpcsFile, int $stackPtr, int $declarePosition): void
	{
		$tokens = $phpcsFile->getTokens();
		$currentPosition = $stackPtr + 1;

		while ($currentPosition < $declarePosition) {
			// Skip comments
			if ($tokens[$currentPosition]['code'] === T_DOC_COMMENT_OPEN_TAG) {
				$currentPosition = $tokens[$currentPosition]['comment_closer'] + 1;
				continue;
			}
			if ($tokens[$currentPosition]['code'] === T_COMMENT) {
				$currentPosition++;
				continue;
			}

			// Check only for empty lines
			if ($tokens[$currentPosition]['code'] === T_WHITESPACE
			    && $tokens[$currentPosition]['content'] === "\n"
			    && $tokens[$currentPosition + 1]['code'] === T_WHITESPACE
			) {
				$fix = $phpcsFile->addFixableError(
					'Empty lines between PHP open tag and declare statement are not allowed',
					$stackPtr,
					'EmptyLinesBeforeDeclare'
				);

				if ($fix) {
					$phpcsFile->fixer->replaceToken($currentPosition + 1, '');
				}
			}
			$currentPosition++;
		}
	}

	/**
	 * Gets the positions of opening and closing parentheses for declare statement
	 *
	 * @param File $phpcsFile The file being scanned
	 * @param int $declarePosition Position of declare keyword
	 * @return array{open: int, close: int}|null Array with positions or null if invalid syntax
	 */
	private function getDeclareTokens(File $phpcsFile, int $declarePosition): ?array
	{
		$tokens = $phpcsFile->getTokens();
		$openParenthesis = $phpcsFile->findNext(T_OPEN_PARENTHESIS, $declarePosition);

		if ($openParenthesis === false || !isset($tokens[$openParenthesis]['parenthesis_closer'])) {
			return null;
		}

		return [
			'open' => $openParenthesis,
			'close' => $tokens[$openParenthesis]['parenthesis_closer']
		];
	}

	/**
	 * Adds missing declare(strict_types=1) statement after PHP open tag
	 *
	 * @param File $phpcsFile The file being scanned
	 * @param int $stackPtr Position to add declare statement
	 */
	private function addMissingDeclare(File $phpcsFile, int $stackPtr): void
	{
		$fix = $phpcsFile->addFixableError(
			self::ERROR_MESSAGE,
			$stackPtr,
			'MissingStrictTypes'
		);

		if ($fix) {
			$tokens = $phpcsFile->getTokens();
			$nextToken = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);

			if ($tokens[$nextToken]['code'] === T_DOC_COMMENT_OPEN_TAG) {
				$insertPosition = $tokens[$nextToken]['comment_closer'] + 1;
			} else {
				$insertPosition = $stackPtr;
			}

			$content = "declare(strict_types=1);\n";
			if (!isset($tokens[$insertPosition + 1]) || $tokens[$insertPosition + 1]['content'] !== "\n") {
				$content .= "\n";
			}

			$phpcsFile->fixer->addContent($insertPosition, $content);
		}
	}

	/**
	 * Validates that declare statement has strict_types=1
	 *
	 * @param File $phpcsFile The file being scanned
	 * @param array<string, mixed> $tokens Token stack
	 * @param array{open: int, close: int} $declareTokens Positions of parentheses
	 * @param int $declarePosition Position of declare keyword
	 */
	private function validateStrictTypes(File $phpcsFile, array $tokens, array $declareTokens, int $declarePosition): void
	{
		if (!$this->hasValidStrictTypes($tokens, $declareTokens)) {
			$fix = $phpcsFile->addFixableError(
				self::ERROR_MESSAGE,
				$declarePosition,
				'InvalidStrictTypes'
			);

			if ($fix) {
				$this->fixStrictTypes($phpcsFile, $declareTokens);
			}
		}
	}

	/**
	 * Checks if declare statement has valid strict_types=1
	 *
	 * @param array<string, mixed> $tokens Token stack
	 * @param array{open: int, close: int} $declareTokens Positions of parentheses
	 * @return bool True if strict_types=1 is present and valid
	 */
	private function hasValidStrictTypes(array $tokens, array $declareTokens): bool
	{
		$strictTypePos = $this->findStrictTypesPosition($tokens, $declareTokens);
		if ($strictTypePos === null) {
			return false;
		}

		return $this->hasValidValue($tokens, $strictTypePos, $declareTokens['close']);
	}

	/**
	 * Finds position of strict_types token in declare statement
	 *
	 * @param array<string, mixed> $tokens Token stack
	 * @param array{open: int, close: int} $declareTokens Positions of parentheses
	 * @return int|null Position of strict_types or null if not found
	 */
	private function findStrictTypesPosition(array $tokens, array $declareTokens): ?int
	{
		for ($i = $declareTokens['open']; $i < $declareTokens['close']; $i++) {
			if ($tokens[$i]['code'] === T_STRING && $tokens[$i]['content'] === 'strict_types') {
				return $i;
			}
		}
		return null;
	}

	/**
	 * Checks if strict_types has valid value (1)
	 *
	 * @param array<string, mixed> $tokens Token stack
	 * @param int $strictTypePos Position of strict_types token
	 * @param int $closePos Position of closing parenthesis
	 * @return bool True if value is 1
	 */
	private function hasValidValue(array $tokens, int $strictTypePos, int $closePos): bool
	{
		$equalPos = $strictTypePos + 1;
		while ($equalPos < $closePos) {
			if ($tokens[$equalPos]['code'] === T_EQUAL) {
				$valuePos = $equalPos + 1;
				while ($valuePos < $closePos) {
					if ($tokens[$valuePos]['code'] === T_LNUMBER) {
						return $tokens[$valuePos]['content'] === self::STRICT_TYPES_VALUE;
					}
					$valuePos++;
				}
				break;
			}
			$equalPos++;
		}
		return false;
	}

	/**
	 * Fixes strict_types value or adds strict_types=1 to declare statement
	 *
	 * @param File $phpcsFile The file being scanned
	 * @param array{open: int, close: int} $declareTokens Positions of parentheses
	 */
	private function fixStrictTypes(File $phpcsFile, array $declareTokens): void
	{
		$tokens = $phpcsFile->getTokens();
		$fixer = $phpcsFile->fixer;

		// If declare is empty - just add strict_types=1
		if ($declareTokens['close'] === $declareTokens['open'] + 1) {
			$fixer->addContent($declareTokens['open'], 'strict_types=1');
			return;
		}

		$strictTypePos = $this->findStrictTypesPosition($tokens, $declareTokens);
		if ($strictTypePos === null) {
			// Add strict_types=1 after last directive or as first one
			$lastComma = $phpcsFile->findPrevious(T_COMMA, $declareTokens['close'] - 1, $declareTokens['open']);
			if ($lastComma === false) {
				$fixer->addContentBefore($declareTokens['close'], ', strict_types=1');
			} else {
				$fixer->addContent($lastComma, ' strict_types=1');
			}
			return;
		}

		// Find and replace value of existing strict_types
		$equalPos = $strictTypePos + 1;
		while ($equalPos < $declareTokens['close']) {
			if ($tokens[$equalPos]['code'] === T_EQUAL) {
				$valuePos = $equalPos + 1;
				while ($valuePos < $declareTokens['close']) {
					if ($tokens[$valuePos]['code'] === T_LNUMBER) {
						$fixer->replaceToken($valuePos, '1');
						return;
					}
					$valuePos++;
				}
				break;
			}
			$equalPos++;
		}
	}
}