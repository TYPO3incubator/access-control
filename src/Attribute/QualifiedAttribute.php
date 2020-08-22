<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Attribute;

/*
 * This file is part of the TYPO3 project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use TYPO3\AccessControl\Utility\AttributeUtility;

/**
 * @api
 */
abstract class QualifiedAttribute extends AbstractAttribute implements QualifiedAttributeInterface
{
    /**
     * Creates a qualified attribute
     *
     * @param string $identifer The primary identifier
     * @param string $names The qualified names
     */
    public function __construct(string $identifier, ?string ...$names)
    {
        $this->meta['identifier'] = $identifier;
        $this->meta['names'] = $names;
    }

    /**
     * @inheritdoc
     */
    public function getIdentifier(): string
    {
        return $this->meta['identifier'];
    }

    /**
     * @inheritdoc
     */
    public function getNames(): array
    {
        return $this->meta['names'];
    }

    public function __toString()
    {
        return $this->getIdentifier();
    }
}
