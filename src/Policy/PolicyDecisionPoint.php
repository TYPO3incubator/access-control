<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Policy;

/*
 * This file is part of the TYPO3 project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\AccessControl\Attribute\AttributeContextInterface;
use TYPO3\AccessControl\Event\PolicyDecisionEvent;

/**
 * @api
 */
final class PolicyDecisionPoint
{
    /**
     * @var AttributeContextInterface
     */
    protected $context;

    /**
     * @var AbstractPolicy
     */
    protected $policy;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var PolicyInformationPoint
     */
    protected $policyInformationPoint;

    /**
     * Creates a policy decision point
     *
     * @param EventDispatcher $eventDispatcher Event dispatcher
     * @param AbstractPolicy $policy Root policy
     * @param PolicyInformationPoint $policyInformationPoint Policy information point
     * @param AttributeContextInterface $context Context
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        AbstractPolicy $policy,
        PolicyInformationPoint $policyInformationPoint,
        ?AttributeContextInterface $context = null
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->policy = $policy;
        $this->policyInformationPoint = $policyInformationPoint;
        $this->context = $context;
    }

    /**
     * Authorize an access request
     *
     * @param array $attributes Attributes of the access request
     * @return PolicyDecision Authorization decision
     */
    public function authorize(array $attributes): PolicyDecision
    {
        $attributes = $this->policyInformationPoint->obtain($attributes, $this->context);

        $decision = $this->policy->evaluate($attributes);

        $this->eventDispatcher->dispatch(new PolicyDecisionEvent($decision, $attributes, $this->context));

        return $decision;
    }
}