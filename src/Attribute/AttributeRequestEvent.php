<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Attribute;

/*
 * This file is part of the TYPO3 project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use TYPO3\AccessControl\Attribute\AttributeInterface;
use TYPO3\AccessControl\Attribute\AttributeContextInterface;

/**
 * @api
 */
final class AttributeRequestEvent
{
    /**
     * @var AttributeContextInterface
     */
    private $context;

    /**
     * @var AttributeInterface
     */
    private $attribute;

    /**
     * @var string
     */
    private $uri;

    /**
     * @var AttributeInterface
     */
    private $target = null;

    public function __construct(
        AttributeInterface $attribute,
        ?AttributeContextInterface $context,
        string $uri
    ) {
        $this->attribute = $attribute;
        $this->context = $context;
        $this->uri = $uri;
    }

    public function getAttribute(): AttributeInterface
    {
        return $this->attribute;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getContext(): ?AttributeContextInterface
    {
        return $this->context;
    }

    public function getTarget(): ?AttributeInterface{
        return $this->target;
    }

    public function setTarget(AttributeInterface $attribute): void {
        $this->target = $attribute;
    }
}
