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
use TYPO3\AccessControl\Policy\PolicySet;

/**
 * Test case
 */
class PolicySetTest extends TestCase
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

        new PolicySet(
            '',
            [
                new Policy(
                    'qux',
                    [
                        new PolicyRule('foo', $this->resolverStub),
                    ],
                    $this->resolverStub,
                    $this->evaluatorProphecy->reveal()
                ),
            ],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal()
        );
    }

    /**
     * @test
     */
    public function constructThrowsWhenPoliciesAreEmpty()
    {
        $this->expectException(InvalidArgumentException::class);

        new PolicySet(
            'qux',
            [],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal()
        );
    }

    /**
     * @test
     */
    public function constructThrowsWhenPoliciesContainInvalidType()
    {
        $this->expectException(InvalidArgumentException::class);

        new PolicySet(
            'qux',
            [
                new Policy(
                    'qux',
                    [
                        new PolicyRule('foo', $this->resolverStub),
                    ],
                    $this->resolverStub,
                    $this->evaluatorProphecy->reveal()
                ),
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

        new PolicySet(
            'baz',
            [
                new Policy(
                    'qux',
                    [
                        new PolicyRule('foo', $this->resolverStub),
                    ],
                    $this->resolverStub,
                    $this->evaluatorProphecy->reveal()
                ),
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

        new PolicySet(
            'bar',
            [
                new Policy(
                    'qux',
                    [
                        new PolicyRule('foo', $this->resolverStub),
                    ],
                    $this->resolverStub,
                    $this->evaluatorProphecy->reveal()
                ),
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
        $subject = new PolicySet(
            'qux',
            [
                new Policy(
                    'qux',
                    [
                        new PolicyRule('foo', $this->resolverStub),
                    ],
                    $this->resolverStub,
                    $this->evaluatorProphecy->reveal()
                ),
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
        $subject = new PolicySet(
            'foo',
            [
                new Policy(
                    'qux',
                    [
                        new PolicyRule('foo', $this->resolverStub),
                    ],
                    $this->resolverStub,
                    $this->evaluatorProphecy->reveal()
                ),
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
        $subject = new PolicySet(
            'foo',
            [
                new Policy(
                    'qux',
                    [
                        new PolicyRule('foo', $this->resolverStub),
                    ],
                    $this->resolverStub,
                    $this->evaluatorProphecy->reveal()
                ),
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
        $subject = new PolicySet(
            'baz',
            [
                new Policy(
                    'qux',
                    [
                        new PolicyRule('foo', $this->resolverStub),
                    ],
                    $this->resolverStub,
                    $this->evaluatorProphecy->reveal()
                ),
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
        $subject = new PolicySet(
            'qux',
            [
                new Policy(
                    'qux',
                    [
                        new PolicyRule('foo', $this->resolverStub),
                    ],
                    $this->resolverStub,
                    $this->evaluatorProphecy->reveal()
                ),
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
        $subject = new PolicySet(
            'foo',
            [
                new Policy(
                    'qux',
                    [
                        new PolicyRule('foo', $this->resolverStub),
                    ],
                    $this->resolverStub,
                    $this->evaluatorProphecy->reveal()
                ),
            ],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal(),
            null,
            null,
            15
        );

        $this->assertSame(15, $subject->getPriority());
    }

    /**
     * @test
     */
    public function getDenyObligationsReturnsEmptyArrayIfNotSetOnConstruct()
    {
        $subject = new PolicySet(
            'bar',
            [
                new Policy(
                    'qux',
                    [
                        new PolicyRule('foo', $this->resolverStub),
                    ],
                    $this->resolverStub,
                    $this->evaluatorProphecy->reveal()
                ),
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
        $subject = new PolicySet(
            'baz',
            [
                new Policy(
                    'qux',
                    [
                        new PolicyRule('foo', $this->resolverStub),
                    ],
                    $this->resolverStub,
                    $this->evaluatorProphecy->reveal()
                ),
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
        $subject = new PolicySet(
            'bar',
            [
                new Policy(
                    'qux',
                    [
                        new PolicyRule('foo', $this->resolverStub),
                    ],
                    $this->resolverStub,
                    $this->evaluatorProphecy->reveal()
                ),
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
        $subject = new PolicySet(
            'bar',
            [
                new Policy(
                    'qux',
                    [
                        new PolicyRule('foo', $this->resolverStub),
                    ],
                    $this->resolverStub,
                    $this->evaluatorProphecy->reveal()
                ),
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
    public function getPoliciesReturnsGivenOneOnConstruct()
    {
        $subject = new PolicySet(
            'baz',
            [
                new Policy(
                    'qux',
                    [
                        new PolicyRule('foo', $this->resolverStub),
                    ],
                    $this->resolverStub,
                    $this->evaluatorProphecy->reveal()
                ),
                new Policy(
                    'bar',
                    [
                        new PolicyRule('baz', $this->resolverStub),
                    ],
                    $this->resolverStub,
                    $this->evaluatorProphecy->reveal()
                ),
            ],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal()
        );

        $this->assertEquals(
            [
                'qux' => new Policy(
                    'qux',
                    [
                        new PolicyRule('foo', $this->resolverStub),
                    ],
                    $this->resolverStub,
                    $this->evaluatorProphecy->reveal()
                ),
                'bar' => new Policy(
                    'bar',
                    [
                        new PolicyRule('baz', $this->resolverStub),
                    ],
                    $this->resolverStub,
                    $this->evaluatorProphecy->reveal()
                ),
            ],
            $subject->getPolicies()
        );
    }

    /**
     * @test
     */
    public function getIteratorReturnsPoliciesArray()
    {
        $subject = new PolicySet(
            'baz',
            [
                new Policy(
                    'foo',
                    [
                        new PolicyRule('baz', $this->resolverStub),
                    ],
                    $this->resolverStub,
                    $this->evaluatorProphecy->reveal()
                ),
            ],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal()
        );

        $this->assertEquals(
            [
                'foo' => new Policy(
                    'foo',
                    [
                        new PolicyRule('baz', $this->resolverStub),
                    ],
                    $this->resolverStub,
                    $this->evaluatorProphecy->reveal()
                ),
            ],
            $subject->getPolicies()
        );
    }

    /**
     * @test
     */
    public function offsetSetThrowsOnCall()
    {
        $this->expectException(NotSupportedMethodException::class);

        $subject = new PolicySet(
            'qux',
            [
                new Policy(
                    'qux',
                    [
                        new PolicyRule('foo', $this->resolverStub),
                    ],
                    $this->resolverStub,
                    $this->evaluatorProphecy->reveal()
                ),
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

        $subject = new PolicySet(
            'bar',
            [
                new Policy(
                    'qux',
                    [
                        new PolicyRule('foo', $this->resolverStub),
                    ],
                    $this->resolverStub,
                    $this->evaluatorProphecy->reveal()
                ),
            ],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal()
        );

        unset($subject['qux']);
    }

    /**
     * @test
     */
    public function offsetExistReturnsTrueWhenPolicyWithGivenIdExist()
    {
        $subject = new PolicySet(
            'foo',
            [
                new Policy(
                    'qux',
                    [
                        new PolicyRule('foo', $this->resolverStub),
                    ],
                    $this->resolverStub,
                    $this->evaluatorProphecy->reveal()
                ),
            ],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal()
        );

        $this->assertTrue(isset($subject['qux']));
    }

    /**
     * @test
     */
    public function offsetExistReturnsFalseWhenPolicyWithGivenIdDoesNotExist()
    {
        $subject = new PolicySet(
            'foo',
            [
                new Policy(
                    'bar',
                    [
                        new PolicyRule('baz', $this->resolverStub),
                    ],
                    $this->resolverStub,
                    $this->evaluatorProphecy->reveal()
                ),
            ],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal()
        );

        $this->assertFalse(isset($subject['qux']));
    }

    /**
     * @test
     */
    public function offsetGetReturnsPolicyRuleWithGivenId()
    {
        $subject = new PolicySet(
            'foo',
            [
                new Policy(
                    'baz',
                    [
                        new PolicyRule('bar', $this->resolverStub),
                    ],
                    $this->resolverStub,
                    $this->evaluatorProphecy->reveal()
                ),
            ],
            $this->resolverStub,
            $this->evaluatorProphecy->reveal()
        );

        $this->assertEquals(
            new Policy(
                'baz',
                [
                    new PolicyRule('bar', $this->resolverStub),
                ],
                $this->resolverStub,
                $this->evaluatorProphecy->reveal()
            ),
            $subject['baz']
        );
    }

    /**
     * @test
     */
    public function offsetGetReturnsNullWhenGivenIdDoesNotExist()
    {
        $subject = new PolicySet(
            'foo',
            [
                new Policy(
                    'baz',
                    [
                        new PolicyRule('bar', $this->resolverStub),
                    ],
                    $this->resolverStub,
                    $this->evaluatorProphecy->reveal()
                ),
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
        $policies = [
            new Policy(
                'foo',
                [
                    new PolicyRule('bar', $this->resolverStub),
                ],
                $this->resolverStub,
                $this->evaluatorProphecy->reveal()
            ),
        ];

        $evaluatorProphecy = $this->prophesize(EvaluatorInterface::class);
        $evaluatorProphecy->process($this->resolverStub, ...$policies)->willReturn(
            new PolicyDecision(PolicyDecision::PERMIT)
        );
        $evaluatorMock = $evaluatorProphecy->reveal();

        $resolverProphecy = $this->prophesize(ResolverInterface::class);
        $resolverProphecy->evaluate('false', [])->willReturn(false);
        $resolverMock = $resolverProphecy->reveal();

        $subject = new PolicySet(
            'qux',
            $policies,
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
        $policies = [
            new Policy(
                'baz',
                [
                    new PolicyRule('bar', $this->resolverStub),
                ],
                $this->resolverStub,
                $this->evaluatorProphecy->reveal()
            ),
        ];

        $evaluatorProphecy = $this->prophesize(EvaluatorInterface::class);
        $evaluatorProphecy->process([], ...$policies)->shouldBeCalled()->willReturn(
            new PolicyDecision(PolicyDecision::NOT_APPLICABLE)
        );
        $evaluatorMock = $evaluatorProphecy->reveal();

        $subject = new PolicySet(
            'bar',
            $policies,
            $this->resolverStub,
            $evaluatorMock
        );

        $subject->evaluate([]);

        $evaluatorProphecy->checkProphecyMethodsPredictions();
    }

    /**
     * @test
     */
    public function evaluateReturnsNonApplicableDecisionIfEvaluatorAlsoDoes()
    {
        $policies = [
            new Policy(
                'foo',
                [
                    new PolicyRule('bar', $this->resolverStub),
                ],
                $this->resolverStub,
                $this->evaluatorProphecy->reveal()
            ),
        ];

        $evaluatorProphecy = $this->prophesize(EvaluatorInterface::class);
        $evaluatorProphecy->process([], ...$policies)->willReturn(
            new PolicyDecision(PolicyDecision::NOT_APPLICABLE)
        );
        $evaluatorMock = $evaluatorProphecy->reveal();

        $subject = new PolicySet(
            'qux',
            $policies,
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
        $policies = [
            new Policy(
                'bar',
                [
                    new PolicyRule('foo', $this->resolverStub),
                ],
                $this->resolverStub,
                $this->evaluatorProphecy->reveal()
            ),
        ];

        $evaluatorProphecy = $this->prophesize(EvaluatorInterface::class);
        $evaluatorProphecy->process([], ...$policies)->willReturn(
            new PolicyDecision(PolicyDecision::PERMIT)
        );
        $evaluatorMock = $evaluatorProphecy->reveal();

        $subject = new PolicySet('qux', $policies, $this->resolverStub, $evaluatorMock);

        $this->assertEquals(new PolicyDecision(PolicyDecision::PERMIT), $subject->evaluate([]));
    }

    /**
     * @test
     */
    public function evaluateReturnsDenyDecisionIfEvaluatorAlsoDoes()
    {
        $policies = [
            new Policy(
                'qux',
                [
                    new PolicyRule('bar', $this->resolverStub),
                ],
                $this->resolverStub,
                $this->evaluatorProphecy->reveal()
            ),
        ];

        $evaluatorProphecy = $this->prophesize(EvaluatorInterface::class);
        $evaluatorProphecy->process([], ...$policies)->willReturn(new PolicyDecision(PolicyDecision::DENY));
        $evaluatorMock = $evaluatorProphecy->reveal();

        $subject = new PolicySet('baz', $policies, $this->resolverStub, $evaluatorMock);

        $this->assertEquals(new PolicyDecision(PolicyDecision::DENY), $subject->evaluate([]));
    }

    /**
     * @test
     */
    public function evaluateReturnsMergedDenyDecision()
    {
        $policies = [
            new Policy(
                'baz',
                [
                    new PolicyRule('foo', $this->resolverStub),
                ],
                $this->resolverStub,
                $this->evaluatorProphecy->reveal()
            ),
            new Policy(
                'bar',
                [
                    new PolicyRule('qux', $this->resolverStub),
                ],
                $this->resolverStub,
                $this->evaluatorProphecy->reveal()
            ),
        ];

        $evaluatorProphecy = $this->prophesize(EvaluatorInterface::class);
        $evaluatorProphecy->process([], ...$policies)->willReturn(
            new PolicyDecision(PolicyDecision::DENY, null, new PolicyObligation('qux'), new PolicyObligation('bar'))
        );
        $evaluatorMock = $evaluatorProphecy->reveal();

        $subject = new PolicySet(
            'qux',
            $policies,
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
        $policies = [
            new Policy(
                'qux',
                [
                    new PolicyRule('foo', $this->resolverStub),
                ],
                $this->resolverStub,
                $this->evaluatorProphecy->reveal()
            ),
            new Policy(
                'baz',
                [
                    new PolicyRule('bar', $this->resolverStub),
                ],
                $this->resolverStub,
                $this->evaluatorProphecy->reveal()
            ),
        ];

        $evaluatorProphecy = $this->prophesize(EvaluatorInterface::class);
        $evaluatorProphecy->process([], ...$policies)->willReturn(
            new PolicyDecision(PolicyDecision::PERMIT, null, new PolicyObligation('foo'), new PolicyObligation('bar'))
        );
        $evaluatorMock = $evaluatorProphecy->reveal();

        $subject = new PolicySet(
            'qux',
            $policies,
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
        $policies = [
            new Policy(
                'bar',
                [
                    new PolicyRule('foo', $this->resolverStub),
                ],
                $this->resolverStub,
                $this->evaluatorProphecy->reveal()
            ),
            new Policy(
                'baz',
                [
                    new PolicyRule('qux', $this->resolverStub),
                ],
                $this->resolverStub,
                $this->evaluatorProphecy->reveal()
            ),
        ];

        $evaluatorProphecy = $this->prophesize(EvaluatorInterface::class);
        $evaluatorProphecy->process([], ...$policies)->willReturn(
            new PolicyDecision(PolicyDecision::PERMIT, $policies[1][0])
        );
        $evaluatorMock = $evaluatorProphecy->reveal();

        $subject = new PolicySet(
            'qux',
            $policies,
            $this->resolverStub,
            $evaluatorMock,
            null,
            null,
            null
        );

        $this->assertEquals(
            new PolicyDecision(
                PolicyDecision::PERMIT,
                $policies[1][0]
            ),
            $subject->evaluate([])
        );
    }
}
