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
final class HighestPriorityEvaluator implements EvaluatorInterface
{
    public function process(array $attributes, EvaluableInterface ...$evaluables): PolicyDecision
    {
        $decision = new PolicyDecision(PolicyDecision::NOT_APPLICABLE);

        usort($evaluables, function ($a, $b) {
            $a = $a->getPriority();
            $b = $b->getPriority();
            return $a === $b ? 0 : $a < $b ? 1 : -1;
        });

        while ($evaluable = current($evaluables)) {
            $next = $evaluable->evaluate($attributes);

            if (!$decision->isApplicable() || $next->getValue() === PolicyDecision::DENY) {
                $decision = $next;
            } else if ($decision->isApplicable() && $next->getValue() === $decision->getValue()) {
                $decision = $decision->merge($next);
            }

            if (next($evaluables) !== false && $decision->isApplicable()
                && $evaluable->getPriority() > current($evaluables)->getPriority()
            ) {
                break;
            }
        }

        return $decision;
    }
}