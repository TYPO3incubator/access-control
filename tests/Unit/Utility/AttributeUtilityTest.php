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
use TYPO3\AccessControl\Utility\AttributeUtility;

/**
 * Test case
 */
class AttributeUtilityTest extends TestCase
{
    public function translateIdentifierProvider()
    {
        return [
            ['FOO42Bar', 'foo42-bar'],
            ['BazBar', 'baz-bar'],
            ['QuxBAR', 'qux-bar'],
            ['fooBAR', 'foo-bar'],
            ['TYPO3\CMS\Foo\Security\AccessControl\Attribute\BarAttribute', 'cms:foo:bar'],
            ['TYPO3\AccessControl\Attribute\QuxAttribute', 'typo3:security:qux'],
            ['Vendor\Bar\Security\AccessControl\Attribute\QuxAttribute', 'bar:qux'],
        ];
    }

    /**
     * @test
     * @dataProvider translateIdentifierProvider
     */
    public function translateIdentifier(string $identifier, string $expected)
    {
        $this->assertEquals(AttributeUtility::translateIdentifier($identifier), $expected);
    }
}
