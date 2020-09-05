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
abstract class AbstractAttribute implements AttributeInterface
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @var array
     */
    private $names;

    /**
     * Creates an attribute
     *
     * @param string $identifer The primary identifier
     * @param string $names The qualified names
     */
    public function __construct(string $identifier, ?string ...$names)
    {
        $this->identifier = $identifier;
        $this->names = $names;
    }

    public function __get(string $property)
    {
        return $this->$property;
    }

    /**
     * @inheritdoc
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @inheritdoc
     */
    public function getNames(): array
    {
        return $this->names;
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->getIdentifier();
    }
}
