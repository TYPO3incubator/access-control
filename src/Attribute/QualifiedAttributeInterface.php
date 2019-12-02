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
interface QualifiedAttributeInterface
{
    /**
     * Gets the local name
     *
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * Gets the qualified name
     *
     * @return string
     */
    public function getName(): string;


    /**
     * Gets the primary namespace
     *
     * @return string
     */
    public function getNamespace(): string;

    /**
     * Gets all namespaces
     *
     * @return string
     */
    public function getNamespaces(): array;
}
