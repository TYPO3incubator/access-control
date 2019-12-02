<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Event;

/*
 * This file is part of the TYPO3 project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use TYPO3\AccessControl\Attribute\AttributeContextInterface;
use TYPO3\AccessControl\Attribute\PrincipalAttribute;

/**
 * @api
 */
final class SubjectRetrievalEvent
{
    /**
     * @var AttributeContextInterface
     */
    private $context;

    /**
     * @var array
     */
    private $principals;

    public function __construct(?AttributeContextInterface $context = null)
    {
        $this->principals = [];
        $this->context = $context;
    }

    public function getContext(): ?AttributeContextInterface
    {
        return $this->context;
    }

    public function addPrincipal(PrincipalAttribute $principal)
    {
        $this->principals[] = $principal;
    }

    public function getPrincipals(): array
    {
        return $this->principals;
    }
}