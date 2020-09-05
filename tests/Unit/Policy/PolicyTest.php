<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Tests\Unit\Policy;

/*
 * This file is part of the TYPO3 project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use TYPO3\AccessControl\Exception\NotSupportedMethodException;
use TYPO3\AccessControl\Policy\Evaluation\EvaluatorInterface;
use TYPO3\AccessControl\Policy\Expression\ResolverInterface;
use TYPO3\AccessControl\Policy\Policy;
use TYPO3\AccessControl\Policy\PolicyDecision;
use TYPO3\AccessControl\Policy\PolicyObligation;
use TYPO3\AccessControl\Policy\PolicyRule;

/**
 * Test case
 */
class PolicyTest extends TestCase
{
    /**
     * @var ResolverInterface
     */
    protected $resolverStub;

    /**
     * @var ObjectProphecy
     */
    protected $evaluatorProphecy;

    public function setUp(): void
    {
        $this->resolverStub = $this->prophesize(ResolverInterface::class)->reveal();
        $this->evaluatorProphecy = $this->prophesize(EvaluatorInterface::class);
    }

    /**
     * @test
     */
    public function constructThrowsWhenIdIsEmpty()
    {
        $this->expectException(InvalidArgumentException::class);

        new Policy(
            '',
            [
                new PolicyRule('qux', $this->resolverStub),
            ],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal()
        );
    }

    /**
     * @test
     */
    public function constructThrowsWhenRulesAreEmpty()
    {
        $this->expectException(InvalidArgumentException::class);

        new Policy(
            'qux',
            [],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal()
        );
    }

    /**
     * @test
     */
    public function constructThrowsWhenRulesContainInvalidType()
    {
        $this->expectException(InvalidArgumentException::class);

        new Policy(
            'qux',
            [
                new PolicyRule('foo', $this->resolverStub),
                'bar',
            ],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal()
        );
    }

    /**
     * @test
     */
    public function constructThrowsWhenDenyObligationsContainInvalidType()
    {
        $this->expectException(InvalidArgumentException::class);

        new Policy(
            'baz',
            [
                new PolicyRule('bar', $this->resolverStub),
            ],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal(),
            null,
            null,
            null,
            [
                new PolicyObligation('qux'),
                'foo',
            ]
        );
    }

    /**
     * @test
     */
    public function constructThrowsWhenPermitObligationsContainInvalidType()
    {
        $this->expectException(InvalidArgumentException::class);

        new Policy(
            'bar',
            [
                new PolicyRule('baz', $this->resolverStub),
            ],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal(),
            null,
            null,
            null,
            null,
            [
                new PolicyObligation('foo'),
                'qux',
            ]
        );
    }

    /**
     * @test
     */
    public function getIdReturnsGivenOneOnConstruct()
    {
        $subject = new Policy(
            'qux',
            [
                new PolicyRule('foo', $this->resolverStub),
            ],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal()
        );

        $this->assertSame('qux', $subject->getId());
    }

    /**
     * @test
     */
    public function getDescriptionReturnsNullIfNotSetOnConstruct()
    {
        $subject = new Policy(
            'foo',
            [
                new PolicyRule('baz', $this->resolverStub),
            ],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal()
        );

        $this->assertSame(null, $subject->getDescription());
    }

    /**
     * @test
     */
    public function getDescriptionReturnsGivenOneOnConstruct()
    {
        $subject = new Policy(
            'foo',
            [
                new PolicyRule('baz', $this->resolverStub),
            ],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal(),
            'bar'
        );

        $this->assertSame('bar', $subject->getDescription());
    }

    /**
     * @test
     */
    public function getTargetReturnsNullIfNotSetOnConstruct()
    {
        $subject = new Policy(
            'baz',
            [
                new PolicyRule('qux', $this->resolverStub),
            ],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal()
        );

        $this->assertNull($subject->getTarget());
    }

    /**
     * @test
     */
    public function getTargetReturnsGivenOneOnConstruct()
    {
        $subject = new Policy(
            'qux',
            [
                new PolicyRule('baz', $this->resolverStub),
            ],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal(),
            null,
            'foo'
        );

        $this->assertSame('foo', $subject->getTarget());
    }

    /**
     * @test
     */
    public function getPriorityReturnsGivenOneOnConstruct()
    {
        $subject = new Policy(
            'foo',
            [
                new PolicyRule('bar', $this->resolverStub),
            ],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal(),
            null,
            null,
            51
        );

        $this->assertSame(51, $subject->getPriority());
    }

    /**
     * @test
     */
    public function getDenyObligationsReturnsEmptyArrayIfNotSetOnConstruct()
    {
        $subject = new Policy(
            'bar',
            [
                new PolicyRule('qux', $this->resolverStub),
            ],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal()
        );

        $this->assertEquals([], $subject->getDenyObligations());
    }

    /**
     * @test
     */
    public function getDenyObligationsReturnsGivenOneOnConstruct()
    {
        $subject = new Policy(
            'baz',
            [
                new PolicyRule('bar', $this->resolverStub),
            ],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal(),
            null,
            null,
            null,
            [new PolicyObligation('bar'), new PolicyObligation('qux')]
        );

        $this->assertEquals([new PolicyObligation('bar'), new PolicyObligation('qux')], $subject->getDenyObligations());
    }

    /**
     * @test
     */
    public function getPermitObligationsReturnsEmptyArrayIfNotSetOnConstruct()
    {
        $subject = new Policy(
            'bar',
            [
                new PolicyRule('qux', $this->resolverStub),
            ],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal()
        );

        $this->assertEquals([], $subject->getDenyObligations());
    }

    /**
     * @test
     */
    public function getPermitObligationsReturnsGivenOneOnConstruct()
    {
        $subject = new Policy(
            'bar',
            [
                new PolicyRule('bar', $this->resolverStub),
            ],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal(),
            null,
            null,
            null,
            null,
            [new PolicyObligation('baz'), new PolicyObligation('qux')]
        );

        $this->assertEquals([new PolicyObligation('baz'), new PolicyObligation('qux')], $subject->getPermitObligations());
    }

    /**
     * @test
     */
    public function getRulesReturnsGivenOneOnConstruct()
    {
        $subject = new Policy(
            'baz',
            [
                new PolicyRule('qux', $this->resolverStub),
                new PolicyRule('bar', $this->resolverStub),
            ],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal()
        );

        $this->assertEquals(
            ['qux' => new PolicyRule('qux', $this->resolverStub), 'bar' => new PolicyRule('bar', $this->resolverStub)],
            $subject->getRules()
        );
    }

    /**
     * @test
     */
    public function getIteratorReturnsRulesArray()
    {
        $subject = new Policy(
            'qux',
            [
                new PolicyRule('foo', $this->resolverStub),
                new PolicyRule('baz', $this->resolverStub),
            ],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal()
        );

        $this->assertEquals(
            ['foo' => new PolicyRule('foo', $this->resolverStub), 'baz' => new PolicyRule('baz', $this->resolverStub)],
            $subject->getIterator()
        );
    }

    /**
     * @test
     */
    public function offsetSetThrowsOnCall()
    {
        $this->expectException(NotSupportedMethodException::class);

        $subject = new Policy(
            'qux',
            [
                new PolicyRule('foo', $this->resolverStub),
            ],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal()
        );

        $subject['foo'] = new PolicyRule('baz', $this->resolverStub);
    }

    /**
     * @test
     */
    public function offsetUnsetThrowsOnCall()
    {
        $this->expectException(NotSupportedMethodException::class);

        $subject = new Policy(
            'bar',
            [
                new PolicyRule('baz', $this->resolverStub),
            ],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal()
        );

        unset($subject['qux']);
    }

    /**
     * @test
     */
    public function offsetExistReturnsTrueWhenRuleWithGivenIdExist()
    {
        $subject = new Policy(
            'foo',
            [
                new PolicyRule('qux', $this->resolverStub),
            ],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal()
        );

        $this->assertTrue(isset($subject['qux']));
    }

    /**
     * @test
     */
    public function offsetExistReturnsFalseWhenRuleWithGivenIdDoesNotExist()
    {
        $subject = new Policy(
            'foo',
            [
                new PolicyRule('bar', $this->resolverStub),
            ],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal()
        );

        $this->assertFalse(isset($subject['foo']));
    }

    /**
     * @test
     */
    public function offsetGetReturnsPolicyRuleWithGivenId()
    {
        $subject = new Policy(
            'foo',
            [
                new PolicyRule('baz', $this->resolverStub),
            ],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal()
        );

        $this->assertEquals(new PolicyRule('baz', $this->resolverStub), $subject['baz']);
    }

    /**
     * @test
     */
    public function offsetGetReturnsNullWhenGivenIdDoesNotExist()
    {
        $subject = new Policy(
            'foo',
            [
                new PolicyRule('baz', $this->resolverStub),
            ],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal()
        );

        $this->assertNull($subject['qux']);
    }

    /**
     * @test
     */
    public function evaluateReturnsNotApplicableDecisionIfTargetDoesNotMatch()
    {
        $rules = [
            new PolicyRule('baz', $this->resolverStub),
        ];

        $evaluatorProphecy = $this->prophesize(EvaluatorInterface::class);
        $evaluatorProphecy->process($this->resolverStub, ...$rules)->willReturn(
            new PolicyDecision(PolicyDecision::PERMIT)
        );
        $evaluatorMock = $evaluatorProphecy->reveal();

        $resolverProphecy = $this->prophesize(ResolverInterface::class);
        $resolverProphecy->evaluate('false', [])->willReturn(false);
        $resolverMock = $resolverProphecy->reveal();

        $subject = new Policy(
            'qux',
            $rules,
            $resolverMock,
            $evaluatorMock,
            null,
            'false'
        );

        $this->assertEquals(new PolicyDecision(PolicyDecision::NOT_APPLICABLE), $subject->evaluate([]));
    }

    /**
     * @test
     */
    public function evaluateUsesEvaluatorGivenOnConstruct()
    {
        $rules = [
            new PolicyRule('baz', $this->resolverStub),
        ];

        $evaluatorProphecy = $this->prophesize(EvaluatorInterface::class);
        $evaluatorProphecy->process([], ...$rules)->shouldBeCalled()->willReturn(
            new PolicyDecision(PolicyDecision::NOT_APPLICABLE)
        );
        $evaluatorMock = $evaluatorProphecy->reveal();

        $subject = new Policy(
            'bar',
            $rules,
            $this->resolverStub,
            $evaluatorMock
        );

        $subject->evaluate([]);

        $evaluatorProphecy->checkProphecyMethodsPredictions();
    }

    /**
     * @test
     */
    public function evaluateReturnsNotApplicableDecisionIfEvaluatorAlsoDoes()
    {
        $rules = [
            new PolicyRule('baz', $this->resolverStub),
        ];

        $evaluatorProphecy = $this->prophesize(EvaluatorInterface::class);
        $evaluatorProphecy->process([], ...$rules)->willReturn(
            new PolicyDecision(PolicyDecision::NOT_APPLICABLE)
        );
        $evaluatorMock = $evaluatorProphecy->reveal();

        $subject = new Policy(
            'qux',
            $rules,
            $this->resolverStub,
            $evaluatorMock
        );

        $this->assertEquals(new PolicyDecision(PolicyDecision::NOT_APPLICABLE), $subject->evaluate([]));
    }

    /**
     * @test
     */
    public function evaluateReturnsPermitDecisionIfEvaluatorAlsoDoes()
    {
        $rules = [
            new PolicyRule('qux', $this->resolverStub),
        ];

        $evaluatorProphecy = $this->prophesize(EvaluatorInterface::class);
        $evaluatorProphecy->process([], ...$rules)->willReturn(
            new PolicyDecision(PolicyDecision::PERMIT)
        );
        $evaluatorMock = $evaluatorProphecy->reveal();

        $subject = new Policy('qux', $rules, $this->resolverStub, $evaluatorMock);

        $this->assertEquals(new PolicyDecision(PolicyDecision::PERMIT), $subject->evaluate([]));
    }

    /**
     * @test
     */
    public function evaluateReturnsDenyDecisionIfEvaluatorAlsoDoes()
    {
        $rules = [
            new PolicyRule('bar', $this->resolverStub),
        ];

        $evaluatorProphecy = $this->prophesize(EvaluatorInterface::class);
        $evaluatorProphecy->process([], ...$rules)->willReturn(
            new PolicyDecision(PolicyDecision::DENY)
        );
        $evaluatorMock = $evaluatorProphecy->reveal();

        $subject = new Policy('baz', $rules, $this->resolverStub, $evaluatorMock);

        $this->assertEquals(new PolicyDecision(PolicyDecision::DENY), $subject->evaluate([]));
    }

    /**
     * @test
     */
    public function evaluateReturnsMergedDenyDecision()
    {
        $rules = [
            new PolicyRule('foo', $this->resolverStub),
            new PolicyRule('baz', $this->resolverStub),
        ];

        $evaluatorProphecy = $this->prophesize(EvaluatorInterface::class);
        $evaluatorProphecy->process([], ...$rules)->willReturn(
            new PolicyDecision(PolicyDecision::DENY, null, new PolicyObligation('qux'), new PolicyObligation('bar'))
        );
        $evaluatorMock = $evaluatorProphecy->reveal();

        $subject = new Policy(
            'qux',
            $rules,
            $this->resolverStub,
            $evaluatorMock,
            null,
            null,
            null,
            [new PolicyObligation('baz'), new PolicyObligation('foo')],
            [new PolicyObligation('bar')]
        );

        $this->assertEquals(
            new PolicyDecision(
                PolicyDecision::DENY,
                null,
                new PolicyObligation('qux'),
                new PolicyObligation('bar'),
                new PolicyObligation('baz'),
                new PolicyObligation('foo')
            ),
            $subject->evaluate([])
        );
    }

    /**
     * @test
     */
    public function evaluateReturnsMergedPermitDecision()
    {
        $rules = [
            new PolicyRule('qux', $this->resolverStub),
            new PolicyRule('baz', $this->resolverStub),
        ];

        $evaluatorProphecy = $this->prophesize(EvaluatorInterface::class);
        $evaluatorProphecy->process([], ...$rules)->willReturn(
            new PolicyDecision(PolicyDecision::PERMIT, null, new PolicyObligation('foo'), new PolicyObligation('bar'))
        );
        $evaluatorMock = $evaluatorProphecy->reveal();

        $subject = new Policy(
            'qux',
            $rules,
            $this->resolverStub,
            $evaluatorMock,
            null,
            null,
            null,
            [new PolicyObligation('qux')],
            [new PolicyObligation('foo'), new PolicyObligation('baz')]
        );

        $this->assertEquals(
            new PolicyDecision(
                PolicyDecision::PERMIT,
                null,
                new PolicyObligation('foo'),
                new PolicyObligation('bar'),
                new PolicyObligation('foo'),
                new PolicyObligation('baz')
            ),
            $subject->evaluate([])
        );
    }

    /**
     * @test
     */
    public function evaluateReturnsDeterminingRule()
    {
        $rules = [
            new PolicyRule('foo', $this->resolverStub),
            new PolicyRule('qux', $this->resolverStub),
        ];

        $evaluatorProphecy = $this->prophesize(EvaluatorInterface::class);
        $evaluatorProphecy->process([], ...$rules)->willReturn(
            new PolicyDecision(PolicyDecision::PERMIT, $rules[0])
        );
        $evaluatorMock = $evaluatorProphecy->reveal();

        $subject = new Policy(
            'qux',
            $rules,
            $this->resolverStub,
            $evaluatorMock,
            null,
            null,
            null
        );

        $this->assertEquals(
            new PolicyDecision(
                PolicyDecision::PERMIT,
                $rules[0]
            ),
            $subject->evaluate([])
        );
    }
}
