<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Policy;

/*
 * This file is part of the TYPO3 project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Psr\Cache\CacheItemPoolInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\AccessControl\Attribute\AttributeContextInterface;
use TYPO3\AccessControl\Attribute\AttributeInterface;
use TYPO3\AccessControl\Attribute\AttributeResolver;
use TYPO3\AccessControl\Event\AttributeRetrievalEvent;
use TYPO3\AccessControl\Event\SubjectRetrievalEvent;

/**
 * @api
 */
final class PolicyInformationPoint
{
    /**
     * @var CacheItemPoolInterface
     */
    protected $cache;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Creates a policy information point.
     *
     * @param EventDispatcherInterface $eventDispatcher Event dispatcher to use
     * @param CacheItemPoolInterface $cache Cache to use
     */
    public function __construct(EventDispatcherInterface $dispatcher, CacheItemPoolInterface $cache = null)
    {
        $this->dispatcher = $dispatcher;
        $this->cache = $cache;
    }

    /**
     * Obtains the given attributes.
     *
     * @param array $attributes Attributes to retrive
     * @param AttributeContextInterface $context Context of the Retrieval
     * @return array Retrived attributes
     */
    public function obtain(array $attributes, ?AttributeContextInterface $context = null): array
    {
        $resolvers = [];

        foreach ($attributes as $attribute) {
            $resolvers[] = new AttributeResolver($attribute, $context, $this->dispatcher);
        }

        return $resolvers;
    }
}