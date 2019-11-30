<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Tests\Unit\Policy;

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

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use TYPO3\AccessControl\Policy\Expression\ResolverInterface;
use TYPO3\AccessControl\Policy\PolicyDecision;
use TYPO3\AccessControl\Policy\PolicyObligation;
use TYPO3\AccessControl\Policy\PolicyRule;

/**
 * Test case
 */
class PolicyDecisionTest extends TestCase
{
    /**
     * @test
     */
    public function constructThrowsWhenNonApplicableIsTriedToCreateWithObligations()
    {
        $this->expectException(InvalidArgumentException::class);

        new PolicyDecision(PolicyDecision::NOT_APPLICABLE, null, new PolicyObligation('foo'));
    }

    /**
     * @test
     */
    public function constructThrowsWhenAnInvaludValueIsUsed()
    {
        $this->expectException(InvalidArgumentException::class);

        new PolicyDecision(3, null, new PolicyObligation('foo'));
    }

    /**
     * @test
     */
    public function getValueReturnsPermitIfSetOnConstruct()
    {
        $subject = new PolicyDecision(PolicyDecision::PERMIT);
        $this->assertEquals(PolicyDecision::PERMIT, $subject->getValue());
    }

    /**
     * @test
     */
    public function getValueReturnsDenyIfSetOnConstruct()
    {
        $subject = new PolicyDecision(PolicyDecision::DENY);
        $this->assertEquals(PolicyDecision::DENY, $subject->getValue());
    }

    /**
     * @test
     */
    public function getValueReturnsNotApplicableIfSetOnConstruct()
    {
        $subject = new PolicyDecision(PolicyDecision::NOT_APPLICABLE);
        $this->assertEquals(PolicyDecision::NOT_APPLICABLE, $subject->getValue());
    }

    /**
     * @test
     */
    public function getRuleReturnsNullIfSetOnConstruct()
    {
        $subject = new PolicyDecision(PolicyDecision::PERMIT, null);
        $this->assertEquals(null, $subject->getRule());
    }

    /**
     * @test
     */
    public function getRuleReturnsNullIfNothingSetOnConstruct()
    {
        $subject = new PolicyDecision(PolicyDecision::DENY);
        $this->assertEquals(null, $subject->getRule());
    }

    /**
     * @test
     */
    public function getRuleReturnsGivenOnConstruct()
    {
        $resolver = $this->prophesize(ResolverInterface::class)->reveal();
        $expected = new PolicyRule('foo', $resolver);
        $subject = new PolicyDecision(PolicyDecision::NOT_APPLICABLE, $expected);
        $this->assertSame($expected, $subject->getRule());
    }

    /**
     * @test
     */
    public function getObligationsReturnsEmptyArrayIfNoneGivenOnConstruct()
    {
        $subject = new PolicyDecision(PolicyDecision::PERMIT);
        $this->assertEmpty($subject->getObligations());
    }

    /**
     * @test
     */
    public function getObligationsReturnsGivenOnConstruct()
    {
        $subject = new PolicyDecision(PolicyDecision::PERMIT, null, new PolicyObligation('bar'), new PolicyObligation('baz'));
        $this->assertEquals([new PolicyObligation('bar'), new PolicyObligation('baz')], $subject->getObligations());
    }

    /**
     * @test
     */
    public function isApplicableReturnsTrueWhenDecisionIsTrue()
    {
        $subject = new PolicyDecision(PolicyDecision::PERMIT);
        $this->assertTrue($subject->isApplicable());
    }

    /**
     * @test
     */
    public function isApplicableReturnsTrueWhenDecisionIsFalse()
    {
        $subject = new PolicyDecision(PolicyDecision::DENY);
        $this->assertTrue($subject->isApplicable());
    }

    /**
     * @test
     */
    public function isApplicableReturnsFalseWhenDecisionIsNull()
    {
        $subject = new PolicyDecision(PolicyDecision::NOT_APPLICABLE);
        $this->assertFalse($subject->isApplicable());
    }

    /**
     * @test
     */
    public function mergeCreatesNewInstance()
    {
        $subject = new PolicyDecision(PolicyDecision::DENY);
        $this->assertNotSame($subject, $subject->merge(new PolicyDecision(PolicyDecision::DENY)));
    }

    /**
     * @test
     */
    public function mergeCreatesNewInstanceWithAllObligations()
    {
        $subject = new PolicyDecision(PolicyDecision::PERMIT, null, new PolicyObligation('bar'));
        $expected = new PolicyDecision(PolicyDecision::PERMIT, null, new PolicyObligation('bar'), new PolicyObligation('foo'));
        $this->assertEquals($expected, $subject->merge(new PolicyDecision(PolicyDecision::PERMIT, null, new PolicyObligation('foo'))));
    }

    /**
     * @test
     */
    public function mergeThrowsWhenUsedOnNonApplicableDecision()
    {
        $this->expectException(InvalidArgumentException::class);

        $subject = new PolicyDecision(PolicyDecision::NOT_APPLICABLE);
        $subject->merge(new PolicyDecision(PolicyDecision::NOT_APPLICABLE));
    }

    /**
     * @test
     */
    public function mergeThrowsWhenBothDecisionsHaveNotTheSameResult()
    {
        $this->expectException(InvalidArgumentException::class);

        $subject = new PolicyDecision(PolicyDecision::PERMIT);
        $subject->merge(new PolicyDecision(PolicyDecision::DENY));
    }
}
