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
use TYPO3\AccessControl\Policy\AbstractPolicy;
use TYPO3\AccessControl\Policy\Evaluation\DenyOverridesEvaluator;
use TYPO3\AccessControl\Policy\Evaluation\FirstApplicableEvaluator;
use TYPO3\AccessControl\Policy\Evaluation\HighestPriorityEvaluator;
use TYPO3\AccessControl\Policy\Evaluation\PermitOverridesEvaluator;
use TYPO3\AccessControl\Policy\Expression\ResolverInterface;
use TYPO3\AccessControl\Policy\Policy;
use TYPO3\AccessControl\Policy\PolicyFactory;
use TYPO3\AccessControl\Policy\PolicyObligation;
use TYPO3\AccessControl\Policy\PolicyRule;
use TYPO3\AccessControl\Policy\PolicySet;

/**
 * Test case
 */
class PolicyFactoryTest extends TestCase
{
    /**
     * @var ResolverInterface
     */
    protected static $resolverStub;

    public function getResolverStub(): ResolverInterface
    {
        if (self::$resolverStub === null) {
            self::$resolverStub = $this->prophesize(ResolverInterface::class)->reveal();
        }

        return self::$resolverStub;
    }

    public function validConfigurationProvider()
    {
        return [
            [
                [
                    'description' => 'bar',
                    'target' => 'foo or bar',
                    'algorithm' => 'denyOverrides',
                    'priority' => 50,
                    'rules' => [
                        'qux' => [
                            'target' => 'bar',
                            'condition' => 'baz',
                        ],
                    ],
                ],
                new Policy(
                    'Root',
                    [
                        'qux' => new PolicyRule(
                            'Root\qux',
                            $this->getResolverStub(),
                            'bar',
                            'baz',
                            null,
                            null,
                            null,
                            null
                        ),
                    ],
                    $this->getResolverStub(),
                    new DenyOverridesEvaluator(),
                    'bar',
                    'foo or bar',
                    50,
                    null,
                    null
                ),
            ],
            [
                [
                    'target' => 'true',
                    'description' => 'baz',
                    'algorithm' => 'highestPriority',
                    'policies' => [
                        'foo' => [
                            'target' => 'true',
                            'description' => 'foo',
                            'algorithm' => 'highestPriority',
                            'priority' => 20,
                            'rules' => [
                                [
                                    'target' => 'false',
                                    'effect' => 'deny',
                                    'priority' => 10,
                                    'obligation' => [
                                        'deny' => [
                                            'baz' => ['qux'],
                                        ],
                                    ],
                                ],
                                [
                                    'condition' => 'true',
                                    'effect' => 'permit',
                                    'priority' => 20,
                                    'obligation' => [
                                        'permit' => [
                                            'foo' => ['bar'],
                                            'baz' => ['bar', 'qux'],
                                        ],
                                        'deny' => [
                                            'bar' => ['foo'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'bar' => [
                            'target' => 'true',
                            'description' => 'bar',
                            'algorithm' => 'permitOverrides',
                            'obligation' => [
                                'permit' => [
                                    'foo' => [],
                                    'bar' => ['baz', 'qux'],
                                ],
                            ],
                            'policies' => [
                                'baz' => [
                                    'target' => 'true',
                                    'algorithm' => 'denyOverrides',
                                    'rules' => [
                                        [
                                            'condition' => 'true',
                                        ],
                                        [
                                            'condition' => 'true',
                                            'effect' => 'permit',
                                        ],
                                    ],
                                ],
                                'qux' => [
                                    'target' => 'true',
                                    'rules' => [
                                        'baz' => [
                                            'target' => 'false',
                                            'condition' => null,
                                            'effect' => 'permit',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                new PolicySet(
                    'Root',
                    [
                        'foo' => new Policy(
                            'Root\foo',
                            [
                                new PolicyRule(
                                    'Root\foo\0',
                                    $this->getResolverStub(),
                                    'false',
                                    null,
                                    PolicyRule::EFFECT_DENY,
                                    10,
                                    [
                                        new PolicyObligation('baz', ['qux']),
                                    ],
                                    null
                                ),
                                new PolicyRule(
                                    'Root\foo\1',
                                    $this->getResolverStub(),
                                    null,
                                    'true',
                                    PolicyRule::EFFECT_PERMIT,
                                    20,
                                    [
                                        new PolicyObligation('bar', ['foo']),
                                    ],
                                    [
                                        new PolicyObligation('foo', ['bar']),
                                        new PolicyObligation('baz', ['bar', 'qux']),
                                    ]
                                ),
                            ],
                            $this->getResolverStub(),
                            new HighestPriorityEvaluator(),
                            'foo',
                            'true',
                            20,
                            null,
                            null
                        ),
                        'bar' => new PolicySet(
                            'Root\bar',
                            [
                                'baz' =>new Policy(
                                    'Root\bar\baz',
                                    [
                                        new PolicyRule(
                                            'Root\bar\baz\0',
                                            $this->getResolverStub(),
                                            null,
                                            'true',
                                            null,
                                            null,
                                            null,
                                            null
                                        ),
                                        new PolicyRule(
                                            'Root\bar\baz\1',
                                            $this->getResolverStub(),
                                            null,
                                            'true',
                                            PolicyRule::EFFECT_PERMIT,
                                            null,
                                            null,
                                            null
                                        ),
                                    ],
                                    $this->getResolverStub(),
                                    new DenyOverridesEvaluator(),
                                    null,
                                    'true',
                                    null,
                                    null,
                                    null
                                ),
                                'qux' => new Policy(
                                    'Root\bar\qux',
                                    [
                                        'baz' => new PolicyRule(
                                            'Root\bar\qux\baz',
                                            $this->getResolverStub(),
                                            'false',
                                            null,
                                            PolicyRule::EFFECT_PERMIT,
                                            null,
                                            null,
                                            null
                                        ),
                                    ],
                                    $this->getResolverStub(),
                                    new FirstApplicableEvaluator(),
                                    null,
                                    'true',
                                    null,
                                    null,
                                    null
                                ),
                            ],
                            $this->getResolverStub(),
                            new PermitOverridesEvaluator(),
                            'bar',
                            'true',
                            null,
                            null,
                            [
                                new PolicyObligation('foo', []),
                                new PolicyObligation('bar', ['baz', 'qux']),
                            ]
                        ),
                    ],
                    $this->getResolverStub(),
                    new HighestPriorityEvaluator(),
                    'baz',
                    'true',
                    null,
                    null,
                    null
                ),
            ],
        ];
    }

    /**
     * @test
     * @dataProvider validConfigurationProvider
     */
    public function buildValidConfiguration(array $configuration, AbstractPolicy $expected)
    {
        $subject = new PolicyFactory();

        $this->assertEquals(
            $expected,
            $subject->build($configuration, $this->getResolverStub())
        );
    }

    public function invalidConfigurationProvider()
    {
        return [
            [
                [
                    'target' => 'foo or bar',
                    'rules' => [],
                ],
            ],
            [
                [
                    'target' => 'foo or bar',
                    'algorithm' => 'denOverrides',
                    'rules' => [
                        'qux' => [
                            'condition' => 'baz',
                        ],
                    ],
                ],
            ],
            [
                [
                    'algorithm' => 'denOverrides',
                    'rules' => [
                        'qux' => [
                            'condition' => 'baz',
                        ],
                    ],
                ],
            ],
            [
                [
                    'rules' => [
                        'qux' => [
                            'condition' => 'baz',
                        ],
                    ],
                    'policies' => [
                        [
                            'target' => 'foo or bar',
                            'rules' => [
                                'qux' => [
                                    'condition' => 'baz',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                [
                    'policies' => [
                        [
                            'target' => 'foo or bar',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider invalidConfigurationProvider
     */
    public function buildThrowsInvalidArgumentForInvalidConfiguration(array $configuration)
    {
        $subject = new PolicyFactory();

        $this->expectException(InvalidArgumentException::class);

        $subject->build($configuration, $this->getResolverStub());
    }
}
