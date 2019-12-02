<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Tests\Unit\Policy\Evaluation;

/*
 * This file is part of the TYPO3 project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use TYPO3\AccessControl\Policy\Evaluation\FirstApplicableEvaluator;
use TYPO3\AccessControl\Policy\PolicyDecision;
use TYPO3\AccessControl\Policy\PolicyObligation;

/**
 * Test case
 */
class FirstApplicableEvaluatorTest extends AbstractEvaluatorTest
{
    public function processDataProvider()
    {
        return [
            [
                new PolicyDecision(PolicyDecision::PERMIT),
                [
                    [PolicyDecision::PERMIT],
                ],
            ],
            [
                new PolicyDecision(PolicyDecision::DENY),
                [
                    [PolicyDecision::DENY],
                ],
            ],
            [
                new PolicyDecision(PolicyDecision::NOT_APPLICABLE),
                [
                    [PolicyDecision::NOT_APPLICABLE],
                ],
            ],
            [
                new PolicyDecision(PolicyDecision::NOT_APPLICABLE),
                [],
            ],
            [
                new PolicyDecision(PolicyDecision::NOT_APPLICABLE),
                [
                    [PolicyDecision::NOT_APPLICABLE],
                    [PolicyDecision::NOT_APPLICABLE],
                    [PolicyDecision::NOT_APPLICABLE],
                    [PolicyDecision::NOT_APPLICABLE],
                ],
            ],
            [
                new PolicyDecision(
                    PolicyDecision::DENY,
                    null,
                    ...[
                        new PolicyObligation('baz'),
                        new PolicyObligation('bar'),
                    ]
                ),
                [
                    [PolicyDecision::NOT_APPLICABLE],
                    [PolicyDecision::NOT_APPLICABLE],
                    [PolicyDecision::DENY, [['baz'], ['bar']]],
                    [PolicyDecision::PERMIT, [['bar'], ['qux']]],
                    [PolicyDecision::NOT_APPLICABLE],
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
                    [PolicyDecision::DENY, [['bar']]],
                    [PolicyDecision::PERMIT],
                    [PolicyDecision::NOT_APPLICABLE],
                    [PolicyDecision::DENY, [['baz'], ['bar']]],
                ],
            ],
            [
                new PolicyDecision(
                    PolicyDecision::PERMIT,
                    null,
                    ...[
                        new PolicyObligation('foo'),
                        new PolicyObligation('bar'),
                    ]
                ),
                [
                    [PolicyDecision::PERMIT, [['foo'], ['bar']]],
                    [PolicyDecision::PERMIT, [['baz'], ['bar']]],
                    [PolicyDecision::DENY],
                    [PolicyDecision::DENY],
                    [PolicyDecision::NOT_APPLICABLE],
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
        $subject = new FirstApplicableEvaluator();

        $this->assertEquals(
            $expected,
            $subject->process(
                [],
                ...$this->buildEvaluables($evaluables)
            )
        );
    }
}
