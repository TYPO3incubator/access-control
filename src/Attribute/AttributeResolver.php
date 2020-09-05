<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Attribute;

/*
 * This file is part of the TYPO3 project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\AccessControl\Event\AttributeRequestEvent;
use TYPO3\AccessControl\Exception\UnknownAttributeException;

/**
 * @internal
 */
final class AttributeResolver implements AttributeInterface {

    /**
     * @var AttributeInterface
     */
    private $attribute;

    /**
     * @var AttributeContextInterface
     */
    private $context;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(
        AttributeInterface $attribute, 
        ?AttributeContextInterface $context,
        EventDispatcherInterface $dispatcher
    ) {
        $this->attribute = $attribute;
        $this->context = $context;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Resolve an attribute
     * 
     * @param string $uri URI to resolve
     * @return AttributeInterface
     */
    public function get(string $uri): AttributeInterface {
        $event = new AttributeRequestEvent($this->attribute, $this->context , $uri);

        $this->dispatcher->dispatch($event);

        if ($event->getTarget() === null) {
            throw new UnknownAttributeException('Unkown attribute ' . $uri . ' requested.');
        }

        return new AttributeResolver($event->getTarget(), $this->context, $this->dispatcher);
    }

    /**
     * Pass through method call
     * 
     * @param string $method Name of the method to call
     * @param array $arguments Arguments to pass
     * @return mixed
     */
    public function __call(string $method, array $arguments) {
        return $this->attribute->$method(...$arguments);
    }

    /**
     * Pass through property get
     * 
     * @param string $property Name of the property
     * @return mixed
     */
    public function __get($property) {
        return $this->attribute->$property;
    }

    /**
     * Pass through property set
     * 
     * @param string $property Name of the property
     * @param mixed $value Value to set
     * @return mixed
     */
    public function __set($property, $value) {
        $this->attribute->$property = $value;
    }

    /**
     * Pass through to string
     */
    public function __toString()
    {
        return (string)$this->attribute;
    }
}