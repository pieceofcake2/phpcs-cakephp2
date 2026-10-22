<?php
declare(strict_types=1);

namespace CakePHP2Namespaced\Sniffs\Commenting;

use CakePHP\Sniffs\Commenting\TypeHintSniff as CakePHPTypeHintSniff;
use PHP_CodeSniffer\Files\File;

/**
 * CakePHP.Commenting.TypeHint, which also works while phpcbf imports the class names of annotations.
 *
 * SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly with searchAnnotations rewrites a doc comment as a
 * whole when it imports a class name: the new comment goes into the first token, and the other tokens become
 * empty. CakePHP.Commenting.TypeHint then parses an empty tag value in the same fixer pass, and
 * phpstan/phpdoc-parser stops phpcbf with a fatal error. This sniff skips a doc comment that another sniff has
 * changed in the current pass; phpcbf checks it again in the next pass, with the new tokens.
 */
class TypeHintSniff extends CakePHPTypeHintSniff
{
    /**
     * @param \PHP_CodeSniffer\Files\File $phpcsFile File
     * `$stackPtr` has no native type because the parent declares it without one.
     *
     * @param int $stackPtr Position of the doc comment open tag
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr): void // phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
    {
        if ($this->isBeingRewritten($phpcsFile, $stackPtr)) {
            return;
        }

        parent::process($phpcsFile, $stackPtr);
    }

    /**
     * Whether another sniff has changed the doc comment in the current fixer pass
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile File
     * @param int $stackPtr Position of the doc comment open tag
     * @return bool
     */
    private function isBeingRewritten(File $phpcsFile, int $stackPtr): bool
    {
        $tokens = $phpcsFile->getTokens();
        $closer = $tokens[$stackPtr]['comment_closer'] ?? $stackPtr;
        for ($pointer = $stackPtr; $pointer <= $closer; $pointer++) {
            // The fixer starts from the original content, before tabs are replaced
            $content = $tokens[$pointer]['orig_content'] ?? $tokens[$pointer]['content'];
            if ($phpcsFile->fixer->getTokenContent($pointer) !== $content) {
                return true;
            }
        }

        return false;
    }
}
