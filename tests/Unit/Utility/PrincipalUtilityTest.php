<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Tests\Unit\Attribute;

/*
 * This file is part of the TYPO3 project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
