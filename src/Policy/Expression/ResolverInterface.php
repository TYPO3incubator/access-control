<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Policy\Expression;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
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