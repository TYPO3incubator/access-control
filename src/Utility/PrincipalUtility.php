<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Utility;

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
class PrincipalUtility
{
    /**
     * Filters a list of principals
     *
     * @param array $principals List to filter
     * @param callable $predicate Prdicate to apply
     * @return array
     */
    public static function filterList(array $principals, callable $predicate): array
    {
        return array_reduce(
            array_filter($principals, $predicate),
            static function ($principals, $principal) {
                $principals[$principal->getName()] = $principal;
                return $principals;
            },
            []
        );
    }
}