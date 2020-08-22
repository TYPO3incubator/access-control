<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Attribute;

/*
 * This file is part of the TYPO3 project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @api
 */
interface QualifiedAttributeInterface extends AttributeInterface
{
    /**
     * Gets the primary identifier
     *
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * Gets the qualified names
     *
     * @return string
     */
    public function getNames(): array;
}
