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
use TYPO3\AccessControl\Attribute\QualifiedAttribute;

/**
 * Test case
 */
class QualifiedAttributeTest extends TestCase
{
    /**
     * @test
     */
    public function instanceProvidesIdentifierProperty()
    {
        $subject = $this->getMockForAbstractClass(
            QualifiedAttribute::class,
            [
                'foo:bar:baz',
                'foo:bar',
                'bar:baz',
            ],
            'FooAttribute'
        );

        $this->assertEquals('foo:bar:baz', $subject->identifier);
    }

    /**
     * @test
     */
    public function instanceProvidesNamesProperty()
    {
        $subject = $this->getMockForAbstractClass(
            QualifiedAttribute::class,
            [
                'foo:bar:baz',
                'foo:bar',
            ],
            'FooAttribute'
        );

        $this->assertEquals([ 'foo:bar' ], $subject->names);
    }

    /**
     * @test
     */
    public function getNamesReturnsNames()
    {
        $subject = $this->getMockForAbstractClass(
            QualifiedAttribute::class,
            [
                'foo:bar:baz',
                'foo:bar',
                'bar:baz',
            ],
            'FooAttribute'
        );

        $this->assertEquals($subject->names, $subject->getNames());
    }

    /**
     * @test
     */
    public function getIdentifierReturnsIdentifier()
    {
        $subject = $this->getMockForAbstractClass(
            QualifiedAttribute::class,
            [
                'foo:bar:baz'
            ],
            'FooAttribute'
        );

        $this->assertEquals($subject->identifier, $subject->getIdentifier());
    }

    /**
     * @test
     */
    public function toStringReturnsIdentifier()
    {
        $subject = $this->getMockForAbstractClass(
            QualifiedAttribute::class,
            [
                'foo:bar:baz'
            ],
            'FooAttribute'
        );

        $this->assertEquals($subject->identifier, (string) $subject);
    }
}
