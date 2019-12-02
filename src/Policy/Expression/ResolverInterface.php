<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Policy\Expression;

/*
 * This file is part of the TYPO3 project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @api
 */
interface ResolverInterface
{
    /**
     * Validates an expression.
     *
     * @param string $expression Expression to validate
     */
    public function validate(string $expression): void;

    /**
     * Evaluates an expression.
     *
     * @param string $expression Expression to evaluate
     * @param array $attributes Attributes to use for the evaluation
     * @return bool Result of the evaluation
     */
    public function evaluate(string $expression, array $attributes): bool;
}