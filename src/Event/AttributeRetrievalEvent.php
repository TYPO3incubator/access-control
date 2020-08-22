<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Event;

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
final class AttributeRetrievalEvent
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
     * @var AttributeInterface
     */
    private $subject;

    public function __construct(
        AbstractAttribute $attribute,
        AttributeInterface $subject,
        ?AttributeContextInterface $context = null
    ) {
        $this->attribute = $attribute;
        $this->subject = $subject;
        $this->context = $context;
    }

    public function getAttribute(): AttributeInterface
    {
        return $this->attribute;
    }

    public function getContext(): ?AttributeContextInterface
    {
        return $this->context;
    }

    public function getSubject(): AttributeInterface
    {
        return $this->subject;
    }
}
