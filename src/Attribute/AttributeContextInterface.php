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
interface AttributeContextInterface
{
    /**
     * Returns a context entry.
     *
     * @param string $key Key of the context entry
     * @return object Context entry
     */
    public function getEntry(string $key): ?object;

    /**
     * Returns a wether a given context entry exist or not.
     *
     * @param string $key Key of the context entry
     * @return bool Wether it exists or not
     */
    public function hasEntry(string $key): bool;

    /**
     * Returns all context entry keys.
     *
     * @return array Entry keys
     */
    public function getKeys(): array;
}