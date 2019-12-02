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