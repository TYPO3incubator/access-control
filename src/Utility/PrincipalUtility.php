<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Utility;

/*
 * This file is part of the TYPO3 project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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