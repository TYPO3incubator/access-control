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
                'bar',
            ],
            'FooAttribute'
        );

        $this->assertEquals('bar', $subject->identifier);
    }

    /**
     * @test
     */
    public function instanceProvidesNamespaceProperty()
    {
        $subject = $this->getMockForAbstractClass(
            QualifiedAttribute::class,
            [
                'bar',
            ],
            'BazAttribute'
        );

        $this->assertEquals('baz', $subject->namespace);
    }

    /**
     * @test
     */
    public function instanceProvidesNameProperty()
    {
        $subject = $this->getMockForAbstractClass(
            QualifiedAttribute::class,
            [
                'baz',
            ],
            'FooAttribute'
        );

        $this->assertEquals('foo:baz', $subject->name);
    }

    /**
     * @test
     */
    public function getNameReturnsName()
    {
        $subject = $this->getMockForAbstractClass(
            QualifiedAttribute::class,
            [
                'qux',
            ],
            'FooAttribute'
        );

        $this->assertEquals($subject->name, $subject->getName());
    }

    /**
     * @test
     */
    public function getNamespaceReturnsNamespace()
    {
        $subject = $this->getMockForAbstractClass(
            QualifiedAttribute::class,
            [
                'foo',
            ],
            'BarAttribute'
        );

        $this->assertEquals($subject->namespace, $subject->getNamespace());
    }

    /**
     * @test
     */
    public function getIdentifierReturnsIdentifier()
    {
        $subject = $this->getMockForAbstractClass(
            QualifiedAttribute::class,
            [
                'bar',
            ],
            'QuxAttribute'
        );

        $this->assertEquals($subject->identifier, $subject->getIdentifier());
    }

    /**
     * @test
     */
    public function toStringReturnsName()
    {
        $subject = $this->getMockForAbstractClass(
            QualifiedAttribute::class,
            [
                'baz',
            ],
            'BarAttribute'
        );

        $this->assertEquals($subject->name, (string) $subject);
    }
}
