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
use TYPO3\AccessControl\Policy\PolicyObligation;

/**
 * Test case
 */
class PolicyObligationTest extends TestCase
{
    /**
     * @test
     */
    public function constructThrowsWhenOperationIsEmpty()
    {
        $this->expectException(InvalidArgumentException::class);

        new PolicyObligation('');
    }

    /**
     * @test
     */
    public function getOperationReturnsGivenOneOnConstruct()
    {
        $subject = new PolicyObligation('foo');
        $this->assertEquals('foo', $subject->getOperation());
    }

    /**
     * @test
     */
    public function getArgumentsReturnsGivenOneOnConstruct()
    {
        $subject = new PolicyObligation('bar', ['baz', 1]);
        $this->assertEquals(['baz', 1], $subject->getArguments());
    }
}
