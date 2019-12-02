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
class AttributeUtility
{
    const NAMESPACE_SEPARATOR = ':';

    public static function translateIdentifier($name)
    {
        return strtolower(preg_replace(
            [
                '/^TYPO3\\\\AccessControl\\\\Attribute/',
                '/(^[^\\\\]+\\\\|\\\\Security\\\\AccessControl\\\\Attribute|Attribute$)/',
                '/\\\\/',
                '/(?<=[A-Z])(?=[A-Z][a-z])|(?<=[^A-Z:])(?=[A-Z])|(?<=[A-Za-z])(?=[^A-Za-z0-9:])/',
            ],
            [
                'TYPO3\TYPO3\Security',
                '',
                ':',
                '-',
            ],
            $name
        ));
    }
}
