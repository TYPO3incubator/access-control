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
final class DenyOverridesEvaluator implements EvaluatorInterface
{
    public function process(array $attributes, EvaluableInterface ...$evaluables): PolicyDecision
    {
        $decision = new PolicyDecision(PolicyDecision::NOT_APPLICABLE);

        foreach ($evaluables as $evaluable) {
            $next = $evaluable->evaluate($attributes);

            if ($next->getValue() === PolicyDecision::DENY) {
                return $next;
            }

            if ($next->isApplicable()) {
                $decision = $decision->isApplicable() ? $decision->merge($next) : $next;
            }
        }

        return $decision;
    }
}