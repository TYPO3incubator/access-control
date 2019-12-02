<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Policy;

/*
 * This file is part of the TYPO3 project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Webmozart\Assert\Assert;

/**
 * @api
 */
final class PolicyDecision
{
    /**
     * @var int
     */
    const NOT_APPLICABLE = 0;

    /**
     * @var int
     */
    const DENY = 1;

    /**
     * @var int
     */
    const PERMIT = 2;

    /**
     * @var int
     */
    private $value;

    /**
     * @var PolicyRule
     */
    private $rule;

    /**
     * @var array
     */
    private $obligations;

    public function __construct(int $value, PolicyRule $rule = null, PolicyObligation ...$obligations)
    {
        Assert::oneOf($value, [self::DENY, self::NOT_APPLICABLE, self::PERMIT]);
        Assert::true(empty($obligations) || $value !== self::NOT_APPLICABLE);

        $this->value = $value;
        $this->rule = $rule;
        $this->obligations = $obligations;
    }

    public function isApplicable(): bool
    {
        return $this->value !== self::NOT_APPLICABLE;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getRule(): ?PolicyRule
    {
        return $this->rule;
    }

    public function getObligations(): array
    {
        return $this->obligations;
    }

    public function merge(self $decision): self
    {
        Assert::notEq($this->value, self::NOT_APPLICABLE);
        Assert::eq($this->value, $decision->getValue());
        Assert::oneOf($this->rule, [$decision->getRule(), null]);

        return new self(
            $this->value,
            $this->rule ?? $decision->getRule(),
            ...array_merge($this->obligations, $decision->getObligations())
        );
    }
}