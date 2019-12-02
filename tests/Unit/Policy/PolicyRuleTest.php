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
use Prophecy\Argument;
use TYPO3\AccessControl\Policy\Expression\ResolverInterface;
use TYPO3\AccessControl\Policy\PolicyDecision;
use TYPO3\AccessControl\Policy\PolicyObligation;
use TYPO3\AccessControl\Policy\PolicyRule;

/**
 * Test case
 */
class PolicyRuleTest extends TestCase
{
    protected $resolverStub;

    public function setUp(): void
    {
        $resolverProphecy = $this->prophesize(ResolverInterface::class);

        $resolverProphecy->evaluate(Argument::exact('true'), [])->willReturn(true);
        $resolverProphecy->evaluate(Argument::exact('false'), [])->willReturn(false);

        $this->resolverStub = $resolverProphecy->reveal();
    }

    /**
     * @test
     */
    public function constructThrowsWhenIdIsEmpty()
    {
        $this->expectException(InvalidArgumentException::class);

        new PolicyRule('', $this->resolverStub);
    }

    /**
     * @test
     */
    public function constructThrowsWhenEffectIsNotDenyOrPermit()
    {
        $this->expectException(InvalidArgumentException::class);

        new PolicyRule('foo', $this->resolverStub, null, null, 'baz');
    }

    /**
     * @test
     */
    public function constructThrowsWhenDenyObligationsContainInvalidType()
    {
        $this->expectException(InvalidArgumentException::class);

        new PolicyRule('foo', $this->resolverStub, null, null, null, null, [1,2,3]);
    }

    /**
     * @test
     */
    public function constructThrowsWhenPermitObligationsContainInvalidType()
    {
        $this->expectException(InvalidArgumentException::class);

        new PolicyRule('foo', $this->resolverStub, null, null, null, null, null, [1,2,3]);
    }

    /**
     * @test
     */
    public function getPriorityReturnsOneIfNotSetOnConstruct()
    {
        $subject = new PolicyRule('foo', $this->resolverStub);

        $this->assertSame(1, $subject->getPriority());
    }

    /**
     * @test
     */
    public function getPriorityReturnsGivenOneOnConstruct()
    {
        $subject = new PolicyRule('foo', $this->resolverStub, null, null, null, 4711);

        $this->assertSame(4711, $subject->getPriority());
    }

    /**
     * @test
     */
    public function getEffectReturnsDenyIfNotSetOnConstruct()
    {
        $subject = new PolicyRule('bar', $this->resolverStub);

        $this->assertSame(PolicyRule::EFFECT_DENY, $subject->getEffect());
    }

    /**
     * @test
     */
    public function getEffectReturnsGivenOneOnConstruct()
    {
        $subject = new PolicyRule('bar', $this->resolverStub, null, null, PolicyRule::EFFECT_PERMIT);

        $this->assertSame(PolicyRule::EFFECT_PERMIT, $subject->getEffect());
    }

    /**
     * @test
     */
    public function getIdReturnsGivenOneOnConstruct()
    {
        $subject = new PolicyRule('baz', $this->resolverStub);

        $this->assertSame('baz', $subject->getId());
    }

    /**
     * @test
     */
    public function getTargetReturnsNullIfNotSetOnConstruct()
    {
        $subject = new PolicyRule('bar', $this->resolverStub);

        $this->assertSame(null, $subject->getTarget());
    }

    /**
     * @test
     */
    public function getTargetReturnsGivenOneOnConstruct()
    {
        $subject = new PolicyRule('bar', $this->resolverStub, 'true');

        $this->assertSame('true', $subject->getTarget());
    }

    /**
     * @test
     */
    public function getConditionReturnsNullIfNotSetOnConstruct()
    {
        $subject = new PolicyRule('qux', $this->resolverStub);

        $this->assertSame(null, $subject->getCondition());
    }

    /**
     * @test
     */
    public function getConditionReturnsGivenOneOnConstruct()
    {
        $subject = new PolicyRule('bar', $this->resolverStub, null, 'false');

        $this->assertSame('false', $subject->getCondition());
    }

    /**
     * @test
     */
    public function getDenyObligationsReturnsGivenOneOnConstruct()
    {
        $subject = new PolicyRule('baz', $this->resolverStub, null, null, null, null, [new PolicyObligation('bar'), new PolicyObligation('qux')]);

        $this->assertEquals([new PolicyObligation('bar'), new PolicyObligation('qux')], $subject->getDenyObligations());
    }

    /**
     * @test
     */
    public function getPermitObligationsReturnsGivenOneOnConstruct()
    {
        $subject = new PolicyRule('qux', $this->resolverStub, null, null, null, null, null, [new PolicyObligation('foo', [1,2,'bar'])]);

        $this->assertEquals([new PolicyObligation('foo', [1,2,'bar'])], $subject->getPermitObligations());
    }

    /**
     * @test
     */
    public function evaluateReturnsApplicableDecisionWhenTargetAndConditionIsNull()
    {
        $subject = new PolicyRule('foo', $this->resolverStub, null, null, PolicyRule::EFFECT_DENY);
        $expected = new PolicyDecision(PolicyDecision::DENY, $subject);

        $this->assertEquals($expected, $subject->evaluate([]));
    }

    /**
     * @test
     */
    public function evaluateReturnsNonApplicableDecisionWhenTargetEvaluatesToFalse()
    {
        $subject = new PolicyRule('qux', $this->resolverStub, 'false');
        $expected = new PolicyDecision(PolicyDecision::NOT_APPLICABLE);

        $this->assertEquals($expected, $subject->evaluate([]));
    }

    /**
     * @test
     */
    public function evaluateReturnsApplicableDecisionWhenTargetEvaluatesToTrueAndConditionIsNotSet()
    {
        $subject = new PolicyRule('baz', $this->resolverStub, 'true', null, PolicyRule::EFFECT_PERMIT);
        $expected = new PolicyDecision(PolicyDecision::PERMIT, $subject);

        $this->assertEquals($expected, $subject->evaluate([]));
    }

    /**
     * @test
     */
    public function evaluateReturnsApplicableDecisionWhenTargetAndConditionEvaluatesToTrue()
    {
        $subject = new PolicyRule('foo', $this->resolverStub, 'true', 'true', PolicyRule::EFFECT_DENY);
        $expected = new PolicyDecision(PolicyDecision::DENY, $subject);

        $this->assertEquals($expected, $subject->evaluate([]));
    }

    /**
     * @test
     */
    public function evaluateReturnsDenyDecisionIfApplicableAndEffectIsNotSetOnConstruct()
    {
        $subject = new PolicyRule('qux', $this->resolverStub);
        $expected = new PolicyDecision(PolicyDecision::DENY, $subject);

        $this->assertEquals($expected, $subject->evaluate([]));
    }

    /**
     * @test
     */
    public function evaluateReturnsDecisionWithPermitObligationsOnPermit()
    {
        $subject = new PolicyRule(
            'bar',
            $this->resolverStub,
            null,
            null,
            PolicyRule::EFFECT_PERMIT,
            null,
            [new PolicyObligation('foo')],
            [new PolicyObligation('baz'), new PolicyObligation('bar')]
        );
        $expected = new PolicyDecision(PolicyDecision::PERMIT, $subject, new PolicyObligation('baz'), new PolicyObligation('bar'));

        $this->assertEquals($expected, $subject->evaluate([]));
    }

    /**
     * @test
     */
    public function evaluateReturnsDecisionWithDenyObligationsOnPermit()
    {
        $subject = new PolicyRule(
            'bar',
            $this->resolverStub,
            null,
            null,
            PolicyRule::EFFECT_DENY,
            null,
            [new PolicyObligation('foo'), new PolicyObligation('qux')],
            [new PolicyObligation('baz')]
        );
        $expected = new PolicyDecision(PolicyDecision::DENY, $subject, new PolicyObligation('foo'), new PolicyObligation('qux'));

        $this->assertEquals($expected, $subject->evaluate([]));
    }
}
