<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Tests\Unit\Policy\Evaluation;

/*
 * This file is part of the TYPO3 project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use TYPO3\AccessControl\Policy\Evaluation\HighestPriorityEvaluator;
use TYPO3\AccessControl\Policy\PolicyDecision;
use TYPO3\AccessControl\Policy\PolicyObligation;

/**
 * Test case
 */
class HighestPriorityEvaluatorTest extends AbstractEvaluatorTest
{
    public function processDataProvider()
    {
        return [
            [
                new PolicyDecision(PolicyDecision::PERMIT),
                [
                    [PolicyDecision::PERMIT, [], 783],
                ],
            ],
            [
                new PolicyDecision(PolicyDecision::DENY),
                [
                    [PolicyDecision::DENY, [], -113],
                ],
            ],
            [
                new PolicyDecision(PolicyDecision::NOT_APPLICABLE),
                [
                    [PolicyDecision::NOT_APPLICABLE, [], 12],
                ],
            ],
            [
                new PolicyDecision(PolicyDecision::NOT_APPLICABLE),
                [],
            ],
            [
                new PolicyDecision(PolicyDecision::NOT_APPLICABLE),
                [
                    [PolicyDecision::NOT_APPLICABLE, [], -4],
                    [PolicyDecision::NOT_APPLICABLE, [], 12],
                    [PolicyDecision::NOT_APPLICABLE, [], 30],
                    [PolicyDecision::NOT_APPLICABLE, [], -2],
                ],
            ],
            [
                new PolicyDecision(
                    PolicyDecision::PERMIT,
                    null,
                    ...[
                        new PolicyObligation('bar'),
                        new PolicyObligation('qux'),
                    ]
                ),
                [
                    [PolicyDecision::NOT_APPLICABLE, [], 100],
                    [PolicyDecision::NOT_APPLICABLE, [], 100],
                    [PolicyDecision::DENY, [['baz'], ['bar']], -20],
                    [PolicyDecision::PERMIT, [['bar'], ['qux']], -1],
                    [PolicyDecision::NOT_APPLICABLE, [], 100],
                ],
            ],
            [
                new PolicyDecision(
                    PolicyDecision::DENY,
                    null,
                    ...[
                        new PolicyObligation('bar'),
                    ]
                ),
                [
                    [PolicyDecision::PERMIT, [], 31],
                    [PolicyDecision::NOT_APPLICABLE, [], 33],
                    [PolicyDecision::DENY, [['bar']], 31],
                    [PolicyDecision::PERMIT, [['baz'], ['bar']], 31],
                ],
            ],
            [
                new PolicyDecision(
                    PolicyDecision::PERMIT,
                    null,
                    ...[
                        new PolicyObligation('foo'),
                        new PolicyObligation('bar'),
                        new PolicyObligation('baz'),
                        new PolicyObligation('bar'),
                    ]
                ),
                [
                    [PolicyDecision::DENY, [], -3],
                    [PolicyDecision::DENY, [], 10],
                    [PolicyDecision::PERMIT, [['foo'], ['bar']], 31],
                    [PolicyDecision::PERMIT, [['baz'], ['bar']], 31],
                    [PolicyDecision::NOT_APPLICABLE, [], 20],
                ],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider processDataProvider
     */
    public function processReturnsDecision(PolicyDecision $expected, array $evaluables)
    {
        $subject = new HighestPriorityEvaluator();

        $this->assertEquals(
            $expected,
            $subject->process(
                [],
                ...$this->buildEvaluables($evaluables)
            )
        );
    }
}
