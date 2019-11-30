<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Tests\Unit\Attribute;

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

use PHPUnit\Framework\TestCase;
use TYPO3\AccessControl\Attribute\PrincipalAttribute;
use TYPO3\AccessControl\Utility\PrincipalUtility;

/**
 * Test case
 */
class PrincipalUtilityTest extends TestCase
{
    public function filterListProvider()
    {
        return [
            [
                [
                    new PrincipalAttribute('foo'),
                    new PrincipalAttribute('bar'),
                    new PrincipalAttribute('baz'),
                ],
                static function (PrincipalAttribute $principalAttribute) {
                    return $principalAttribute->getIdentifier() !== 'bar';
                },
                [
                    'typo3:security:principal:foo' => new PrincipalAttribute('foo'),
                    'typo3:security:principal:baz' => new PrincipalAttribute('baz'),
                ],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider filterListProvider
     */
    public function filterList(array $principals, callable $predicate, array $expected)
    {
        $this->assertEquals(PrincipalUtility::filterList($principals, $predicate), $expected);
    }
}
