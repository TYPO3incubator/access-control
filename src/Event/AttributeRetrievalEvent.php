<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Event;

/*
 * This file is part of the TYPO3 project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use TYPO3\AccessControl\Attribute\AbstractAttribute;
use TYPO3\AccessControl\Attribute\AttributeContextInterface;
use TYPO3\AccessControl\Attribute\SubjectAttribute;

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
     * @var AbstractAttribute
     */
    private $attribute;

    /**
     * @var SubjectAttribute
     */
    private $subjectAttribute;

    public function __construct(
        AbstractAttribute $attribute,
        SubjectAttribute $subjectAttribute,
        ?AttributeContextInterface $context = null
    ) {
        $this->attribute = $attribute;
        $this->subjectAttribute = $subjectAttribute;
        $this->context = $context;
    }

    public function getAttribute(): AbstractAttribute
    {
        return $this->attribute;
    }

    public function getContext(): ?AttributeContextInterface
    {
        return $this->context;
    }

    public function getSubject(): SubjectAttribute
    {
        return $this->subjectAttribute;
    }
}
