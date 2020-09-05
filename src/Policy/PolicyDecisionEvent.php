<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Policy;

/*
 * This file is part of the TYPO3 project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use TYPO3\AccessControl\Attribute\AttributeContextInterface;
use TYPO3\AccessControl\Policy\PolicyDecision;

/**
 * @api
 */
final class PolicyDecisionEvent
{
    /**
     * @var PolicyDecision
     */
    private $decision;

    /**
     * @var array
     */
    private $context;

    /**
     * @var array
     */
    private $attributes;

    public function __construct(
        PolicyDecision $decision,
        array $attributes,
        ?AttributeContextInterface $context = null
    ) {
        $this->decision = $decision;
        $this->attributes = $attributes;
        $this->context = $context;
    }

    public function getDecision(): PolicyDecision
    {
        return $this->decision;
    }

    public function getContext(): ?AttributeContextInterface
    {
        return $this->context;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
