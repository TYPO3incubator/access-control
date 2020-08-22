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
use TYPO3\AccessControl\Attribute\AttribtueInterface;
use TYPO3\AccessControl\Event\AttributeRetrievalEvent;
use TYPO3\AccessControl\Event\SubjectRetrievalEvent;

/**
 * @api
 */
final class PolicyInformationPoint
{
    const SUBJECT_ATTRIBUTE = 'subject';

    /**
     * @var CacheItemPoolInterface
     */
    protected $cache;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * Creates a policy information point.
     *
     * @param EventDispatcherInterface $eventDispatcher Event dispatcher to use
     * @param CacheItemPoolInterface $cache Cache to use
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, CacheItemPoolInterface $cache = null)
    {
        $this->eventDispatcher = $eventDispatcher;
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
        $attributes = array_filter($attributes, static function ($key) {
            return $key !== self::SUBJECT_ATTRIBUTE;
        }, ARRAY_FILTER_USE_KEY);

        $subjectEvent = new SubjectRetrievalEvent($context);

        $this->eventDispatcher->dispatch($subjectEvent);

        foreach ($attributes as $attribute) {
            $this->eventDispatcher->dispatch(
                new AttributeRetrievalEvent($attribute, $subjectAttribute, $context)
            );
        }

        $attributes[self::SUBJECT_ATTRIBUTE] = $subjectEvent->getSubject();

        return $attributes;
    }
}