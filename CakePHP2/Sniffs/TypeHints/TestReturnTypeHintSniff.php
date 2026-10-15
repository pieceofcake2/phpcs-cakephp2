<?php
declare(strict_types=1);

namespace CakePHP2\Sniffs\TypeHints;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FunctionHelper;

/**
 * Requires a native return type on every method and function of the tests.
 *
 * CakePHP 2 code outside the tests keeps its signatures without native types, and the CakePHP standard
 * does not check return types in `tests/` at all. Test methods are only called by PHPUnit, which declares
 * `setUp(): void` and its other template methods with native types, so the tests use them everywhere:
 * `testFoo(): void`, `fooProvider(): array`. ruleset.xml applies this sniff to the tests only.
 *
 * A method that does not return a value is fixed with `: void` (phpcbf).
 */
class TestReturnTypeHintSniff implements Sniff
{
    public const CODE_MISSING_NATIVE_TYPE_HINT = 'MissingNativeTypeHint';

    /**
     * @return array<int, int|string>
     */
    public function register(): array
    {
        return [T_FUNCTION];
    }

    /**
     * @param \PHP_CodeSniffer\Files\File $phpcsFile File
     * `$pointer` has no native type because PHP_CodeSniffer 3 (installed on PHP 8.0) declares it without one.
     *
     * @param int $pointer Position of the function keyword
     * @return void
     */
    public function process(File $phpcsFile, $pointer): void // phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
    {
        $name = strtolower(FunctionHelper::getName($phpcsFile, $pointer));
        if (in_array($name, ['__construct', '__destruct', '__clone'], true)) {
            return;
        }
        if (FunctionHelper::findReturnTypeHint($phpcsFile, $pointer) !== null) {
            return;
        }

        $message = sprintf(
            '%s %s() does not have a native return type; tests declare it, e.g. ": void".',
            FunctionHelper::getTypeLabel($phpcsFile, $pointer),
            FunctionHelper::getFullyQualifiedName($phpcsFile, $pointer),
        );
        if (FunctionHelper::isAbstract($phpcsFile, $pointer) || FunctionHelper::returnsValue($phpcsFile, $pointer)) {
            $phpcsFile->addError($message, $pointer, self::CODE_MISSING_NATIVE_TYPE_HINT);

            return;
        }

        if ($phpcsFile->addFixableError($message, $pointer, self::CODE_MISSING_NATIVE_TYPE_HINT)) {
            $closer = $phpcsFile->getTokens()[$pointer]['parenthesis_closer'];
            $phpcsFile->fixer->addContent($closer, ': void');
        }
    }
}
