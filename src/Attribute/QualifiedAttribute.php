<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Attribute;

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

use TYPO3\AccessControl\Utility\AttributeUtility;

/**
 * @api
 */
abstract class QualifiedAttribute extends AbstractAttribute implements QualifiedAttributeInterface
{
    /**
     * @var array
     */
    private static $namespaces = [];

    /**
     * Creates a qualified attribute
     *
     * @param string $identifer The local name
     */
    public function __construct(string $identifier)
    {
        if (!isset(self::$namespaces[static::class])) {
            $classes = array_merge([static::class], class_parents(static::class));

            foreach ($classes as $class) {
                self::$namespaces[static::class][] = AttributeUtility::translateIdentifier($class);
            }
        }

        $this->meta['namespaces'] = self::$namespaces[static::class];
        $this->meta['namespace'] = self::$namespaces[static::class][0];
        $this->meta['identifier'] = AttributeUtility::translateIdentifier($identifier);
        $this->meta['name'] = self::$namespaces[static::class][0]
            . AttributeUtility::NAMESPACE_SEPARATOR
            . $this->meta['identifier'];
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
    public function getName(): string
    {
        return $this->meta['name'];
    }

    /**
     * @inheritdoc
     */
    public function getNamespace(): string
    {
        return $this->meta['namespace'];
    }

    /**
     * @inheritdoc
     */
    public function getNamespaces(): array
    {
        return $this->meta['namespaces'];
    }

    public function __toString()
    {
        return $this->getName();
    }
}
