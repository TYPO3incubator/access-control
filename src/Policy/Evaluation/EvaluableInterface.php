<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Policy\Evaluation;

/*
 * This file is part of the TYPO3 project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use TYPO3\AccessControl\Policy\PolicyDecision;

/**
 * @internal
 */
interface EvaluableInterface
{
    public function evaluate(array $attributes): PolicyDecision;

    public function getPriority(): int;
}