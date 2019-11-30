<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Event;

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

use TYPO3\AccessControl\Attribute\AbstractAttribute;
use TYPO3\AccessControl\Attribute\AttributeContextInterface;
use TYPO3\AccessControl\Attribute\SubjectAttribute;

/**
 * @api
 */
final class AttributeRetrivalEvent
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
