<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Policy;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Psr\Cache\CacheItemPoolInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\AccessControl\Attribute\AttributeContextInterface;
use TYPO3\AccessControl\Attribute\SubjectAttribute;
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

        $subjectAttribute = new SubjectAttribute(...$subjectEvent->getPrincipals());

        foreach ($attributes as $attribute) {
            $this->eventDispatcher->dispatch(
                new AttributeRetrievalEvent($attribute, $subjectAttribute, $context)
            );
        }

        $attributes[self::SUBJECT_ATTRIBUTE] = $subjectAttribute;

        return $attributes;
    }
}