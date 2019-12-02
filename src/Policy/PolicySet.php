<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Policy;

/*
 * This file is part of the TYPO3 project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use TYPO3\AccessControl\Policy\Evaluation\EvaluatorInterface;
use TYPO3\AccessControl\Policy\Expression\ResolverInterface;
use Webmozart\Assert\Assert;

/**
 * @internal
 */
final class PolicySet extends AbstractPolicy
{
    /**
     * @var EvaluatorInterface
     */
    private $evaluator;

    /**
     * @var AbstractPolicy[]
     */
    private $policies;

    public function __construct(
        string $id,
        array $policies,
        ResolverInterface $resolver,
        EvaluatorInterface $evaluator,
        ?string $description = null,
        ?string $target = null,
        ?int $priority = null,
        ?array $denyObligations = null,
        ?array $permitObligations = null
    ) {
        Assert::notEmpty($policies, sprintf('Policy set %s must have at least one policy', $id));
        Assert::allIsInstanceOf($policies, AbstractPolicy::class);

        parent::__construct($id, $resolver, $description, $target, $priority, $denyObligations, $permitObligations);

        $this->evaluator = $evaluator;
        $this->policies = array_combine(array_map(function ($policy) {
            return $policy->getId();
        }, $policies), array_values($policies));
    }

    public function evaluate(array $attributes): PolicyDecision
    {
        if ($this->target !== null && !$this->resolver->evaluate($this->target, $attributes)) {
            return new PolicyDecision(PolicyDecision::NOT_APPLICABLE);
        }

        $decision = $this->evaluator->process($attributes, ...array_values($this->policies));

        if (!$decision->isApplicable()) {
            return $decision;
        }

        return $decision->merge(
            new PolicyDecision(
                $decision->getValue(),
                $decision->getRule(),
                ...$decision->getValue() === PolicyDecision::PERMIT
                    ? $this->permitObligations : $this->denyObligations
            )
        );
    }

    public function getIterator()
    {
        return $this->policies;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->policies[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->policies[$offset] ?? null;
    }

    /**
     * @return AbstractPolicies[]
     */
    public function getPolicies(): array
    {
        return $this->policies;
    }
}