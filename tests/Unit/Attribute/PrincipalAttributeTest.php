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

/**
 * Test case
 */
class PrincipalAttributeTest extends TestCase
{
    /**
     * @test
     */
    public function constructPropagatesIdentifier()
    {
        $subject = new PrincipalAttribute('bar');

        $this->assertEquals('bar', $subject->identifier);
    }

    /**
     * @test
     */
    public function constructPropagatesName()
    {
        $subject = new PrincipalAttribute('foo');

        $this->assertEquals('typo3:security:principal:foo', $subject->name);
    }

    /**
     * @test
     */
    public function constructPropagatesNamespace()
    {
        $subject = new PrincipalAttribute('qux');

        $this->assertEquals('typo3:security:principal', $subject->namespace);
    }
}
