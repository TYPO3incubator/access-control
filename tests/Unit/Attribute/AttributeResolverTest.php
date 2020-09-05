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
use TYPO3\AccessControl\Attribute\AttributeInterface;
use TYPO3\AccessControl\Tests\Unit\Fixture\Attribute;

/**
 * Test case
 */
class AttributeResolverTest extends TestCase
{

    public function setUp(): void
    {
        $this->attributeProphecy = $this->prophesize(Attribute::class);
        $this->dispatcherProphecy = $this->prophesize(EventDispatcherInterface::class);
    }

    /**
     * @test
     */
    public function instancePassesUnknownMethodCallsToAttribute()
    {
        $this->attributeProphecy->bar()->shouldBeCalledTimes(1);
        $this->attributeProphecy->baz(['foo', 'bar'])->shouldBeCalledTimes(1);
        $this->attributeProphecy->qux('foo', 'bar')->shouldBeCalledTimes(1);

        $subject = new AttributeResolver($this->attributeProphecy->reveal(), null, $this->dispatcherProphecy->reveal());

        $subject->bar();
        $subject->baz(['foo', 'bar']);
        $subject->qux('foo', 'bar');
    }

    /**
     * @test
     */
    public function instancePassesUnknownPropertyReadsToAttribute()
    {
        $this->attributeProphecy->foo = 123;

        $subject = new AttributeResolver($this->attributeProphecy->reveal(), null, $this->dispatcherProphecy->reveal());

        $this->assertEquals(123, $subject->foo);
    }

    /**
     * @test
     */
    public function instancePassesUnknownPropertyWritesToAttribute()
    {
        $subject = new AttributeResolver($this->attributeProphecy->reveal(), null, $this->dispatcherProphecy->reveal());

        $subject->foo = 123;

        $this->assertEquals(123, $this->attributeProphecy->foo);
    }

    /**
     * @test
     */
    public function instanceDispatchEventWhenGetIsCalled()
    {
        $target = $this->prophesize(Attribute::class)->reveal();

        $target->foo = 345;

        $this->dispatcherProphecy->dispatch(Argument::any())->will(function(array $arguments) use ($target) {
            $arguments[0]->setTarget($target);
        })->shouldBeCalledTimes(1);

        $subject = new AttributeResolver($this->attributeProphecy->reveal(), null, $this->dispatcherProphecy->reveal());

        $this->assertEquals(345, $subject->get('foo:bar')->foo);
    }
}
