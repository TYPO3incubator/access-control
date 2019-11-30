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
