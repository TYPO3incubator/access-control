<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Tests\Unit\Policy\Evaluation;

/*
 * This file is part of the TYPO3 project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use TYPO3\AccessControl\Policy\Evaluation\EvaluableInterface;
use TYPO3\AccessControl\Policy\PolicyDecision;
use TYPO3\AccessControl\Policy\PolicyObligation;

/**
 * Test case
 */
abstract class AbstractEvaluatorTest extends TestCase
{
    protected function buildEvaluables(array $data)
    {
        return array_map(function ($data) {
            $stub = $this->prophesize(EvaluableInterface::class);
            $stub->evaluate(Argument::any())->willReturn(
                new PolicyDecision(
                    $data[0],
                    null,
                    ...array_map(function ($data) {
                        return new PolicyObligation($data[0]);
                    }, $data[1] ?? [])
                )
            );

            if (isset($data[2])) {
                $stub->getPriority(Argument::any())->willReturn($data[2]);
            }

            return $stub->reveal();
        }, $data);
    }
}
