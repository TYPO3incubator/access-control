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
use TYPO3\AccessControl\Policy\PolicyDecision;
use Webmozart\Assert\Assert;

/**
 * @internal
 */
final class Policy extends AbstractPolicy
{
    /**
     * @var EvaluatorInterface
     */
    private $evaluator;

    /**
     * @var array
     */
    private $rules;

    public function __construct(
        string $id,
        array $rules,
        ResolverInterface $resolver,
        EvaluatorInterface $evaluator,
        ?string $description = null,
        ?string $target = null,
        ?int $priority = null,
        ?array $denyObligations = null,
        ?array $permitObligations = null
    ) {
        Assert::notEmpty($rules);
        Assert::allIsInstanceOf($rules, PolicyRule::class);

        parent::__construct($id, $resolver, $description, $target, $priority, $denyObligations, $permitObligations);

        $this->evaluator = $evaluator;
        $this->rules = array_combine(array_map(function ($rule) {
            return $rule->getId();
        }, $rules), array_values($rules));
    }

    public function evaluate(array $attributes): PolicyDecision
    {
        if ($this->target !== null && !$this->resolver->evaluate($this->target, $attributes)) {
            return new PolicyDecision(PolicyDecision::NOT_APPLICABLE);
        }

        $decision = $this->evaluator->process($attributes, ...array_values($this->rules));

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
        return $this->rules;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->rules[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->rules[$offset] ?? null;
    }

    /**
     * @return PolicyRule[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }
}