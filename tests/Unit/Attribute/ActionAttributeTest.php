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
use TYPO3\AccessControl\Attribute\ActionAttribute;

/**
 * Test case
 */
class ActionAttributeTest extends TestCase
{
    /**
     * @test
     */
    public function constructPropagatesNamespaceAsIdentifier()
    {
        $subject = $this->getMockForAbstractClass(
            ActionAttribute::class,
            [],
            'ActionAttribute'
        );

        $this->assertEquals($subject->namespace, $subject->identifier);
    }

    /**
     * @test
     */
    public function constructPropagatesNamespaceAsName()
    {
        $subject = $this->getMockForAbstractClass(
            ActionAttribute::class,
            [],
            'ActionAttribute'
        );

        $this->assertEquals($subject->namespace, $subject->name);
    }
}
