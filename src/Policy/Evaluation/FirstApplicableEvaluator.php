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
final class FirstApplicableEvaluator implements EvaluatorInterface
{
    public function process(array $attributes, EvaluableInterface ...$evaluables): PolicyDecision
    {
        foreach ($evaluables as $evaluable) {
            $next = $evaluable->evaluate($attributes);

            if ($next->isApplicable()) {
                return $next;
            }
        }

        return new PolicyDecision(PolicyDecision::NOT_APPLICABLE);
    }
}