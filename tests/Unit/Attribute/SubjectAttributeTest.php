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
use TYPO3\AccessControl\Attribute\PrincipalAttribute;
use TYPO3\AccessControl\Attribute\SubjectAttribute;

/**
 * Test case
 */
class SubjectAttributeTest extends TestCase
{
    /**
     * @test
     */
    public function constructPropagatesPrincipals()
    {
        $subject = new SubjectAttribute(new PrincipalAttribute('foo'));

        $this->assertEquals('foo', $subject->principals['typo3:security:principal:foo']->getIdentifier());
    }
}
