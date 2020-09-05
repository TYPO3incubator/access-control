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
use Prophecy\Argument;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\AccessControl\Attribute\AttributeResolver;
use TYPO3\AccessControl\Attribute\AbstractAttribute;
use TYPO3\AccessControl\Attribute\AttributeContextInterface;
use TYPO3\AccessControl\Attribute\AttributeNotFoundException;

/**
 * Test case
 */
class AttributeResolverTest extends TestCase
{

    public function setUp(): void
    {
        $this->contextProphecy = $this->prophesize(AttributeContextInterface::class);
        $this->dispatcherProphecy = $this->prophesize(EventDispatcherInterface::class);
    }

    /**
     * @test
     */
    public function instancePassesUnknownMethodCallsToAttribute()
    {
        $attribute = $this->getMockForAbstractClass(
            AbstractAttribute::class,
            [
                'foo:bar:baz',
                'foo:bar',
                'bar:baz',
            ],
            'FooAttribute'
        );
        $subject = new AttributeResolver($attribute, null, $this->dispatcherProphecy->reveal());

        $this->assertEquals('foo:bar:baz', $subject->getIdentifier());
        $this->assertEquals(['foo:bar', 'bar:baz'], $subject->getNames());
    }

    /**
     * @test
     */
    public function instancePassesUnknownPropertyReadsToAttribute()
    {
        $attribute = $this->getMockForAbstractClass(
            AbstractAttribute::class,
            [
                'foo:bar:baz',
                'foo:bar',
                'bar:baz',
            ],
            'FooAttribute'
        );
        $subject = new AttributeResolver($attribute, null, $this->dispatcherProphecy->reveal());

        $this->assertEquals('foo:bar:baz', $subject->identifier);
        $this->assertEquals(['foo:bar', 'bar:baz'], $subject->names);
    }

    /**
     * @test
     */
    public function instanceDispatchEventWhenGetIsCalled()
    {
        $context = $this->contextProphecy->reveal();

        $attribute = $this->getMockForAbstractClass(
            AbstractAttribute::class,
            [
                'bar:baz',
                'bar:baz',
            ],
            'BarAttribute'
        );

        $target = $this->getMockForAbstractClass(
            AbstractAttribute::class,
            [
                'foo:bar',
                'bar:baz',
            ],
            'FooAttribute'
        );

        $this->dispatcherProphecy->dispatch(Argument::any())->will(function(array $arguments) use ($target) {
            $arguments[0]->setTarget($target);
        })->shouldBeCalledTimes(3);

        $subject = new AttributeResolver($attribute, $context, $this->dispatcherProphecy->reveal());

        $this->assertEquals('foo:bar', $subject->get('foo:bar')->identifier);
        $this->assertEquals(['bar:baz'], $subject->get('foo:bar')->names);
        $this->assertEquals($context, $subject->get('foo:bar')->getContext());
    }

    /**
     * @test
     */
    public function instanceThrowsWhenGetFailed()
    {
        $attribute = $this->getMockForAbstractClass(
            AbstractAttribute::class,
            [
                'bar:baz',
                'bar:baz',
            ],
            'BarAttribute'
        );

        $this->dispatcherProphecy->dispatch(Argument::any())->shouldBeCalledTimes(1);

        $this->expectException(AttributeNotFoundException::class);

        $subject = new AttributeResolver($attribute, null, $this->dispatcherProphecy->reveal());

        $subject->get('foo:bar');
    }
}
