<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Policy;

/*
 * This file is part of the TYPO3 project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use TYPO3\AccessControl\Policy\Evaluation\EvaluableInterface;
use TYPO3\AccessControl\Policy\Expression\ResolverInterface;
use TYPO3\AccessControl\Policy\PolicyDecision;
use Webmozart\Assert\Assert;

/**
 * @internal
 */
final class PolicyRule implements EvaluableInterface
{
    /**
     * @var string
     */
    const EFFECT_DENY = 'deny';

    /**
     * @var string
     */
    const EFFECT_PERMIT = 'permit';

    /**
     * @var string
     */
    private $id;

    /**
     * @var ResolverInterface
     */
    protected $resolver;

    /**
     * @var string
     */
    private $target;

    /**
     * @var string
     */
    private $condition;

    /**
     * @var string
     */
    private $effect;

    /**
     * @var int
     */
    private $priority;

    /**
     * @var PolicyObligations[]
     */
    private $denyObligations;

    /**
     * @var PolicyObligations[]
     */
    private $permitObligations;

    public function __construct(
        string $id,
        ResolverInterface $resolver,
        ?string $target = null,
        ?string $condition = null,
        ?string $effect = null,
        ?int $priority = null,
        ?array $denyObligations = null,
        ?array $permitObligations = null
    ) {
        Assert::stringNotEmpty($id, '$id must not be empty');
        Assert::allIsInstanceOf($denyObligations ?? [], PolicyObligation::class);
        Assert::allIsInstanceOf($permitObligations ?? [], PolicyObligation::class);
        Assert::oneOf(
            $effect,
            [
                null,
                self::EFFECT_DENY,
                self::EFFECT_PERMIT,
            ],
            '$effect must be "deny" or "permit" if set'
        );

        $this->id = $id;
        $this->resolver = $resolver;
        $this->target = $target;
        $this->condition = $condition;
        $this->effect = $effect ?? self::EFFECT_DENY;
        $this->priority = $priority ?? 1;
        $this->denyObligations = $denyObligations ?? [];
        $this->permitObligations = $permitObligations ?? [];
    }

    public function evaluate(array $attributes): PolicyDecision
    {
        if ($this->target !== null && !$this->resolver->evaluate($this->target, $attributes)) {
            return new PolicyDecision(PolicyDecision::NOT_APPLICABLE);
        }

        if ($this->condition !== null && !$this->resolver->evaluate($this->condition, $attributes)) {
            return new PolicyDecision(PolicyDecision::NOT_APPLICABLE);
        }

        $decision = $this->effect === self::EFFECT_PERMIT ? PolicyDecision::PERMIT : PolicyDecision::DENY;
        $obligations = $decision === PolicyDecision::PERMIT ? $this->permitObligations : $this->denyObligations;

        return new PolicyDecision($decision, $this, ...$obligations);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function getCondition(): ?string
    {
        return $this->condition;
    }

    public function getEffect(): string
    {
        return $this->effect;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @return PolicyObligation[]
     */
    public function getDenyObligations(): array
    {
        return $this->denyObligations;
    }

    /**
     * @return PolicyObligation[]
     */
    public function getPermitObligations(): array
    {
        return $this->permitObligations;
    }
}