<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Event;

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

use TYPO3\AccessControl\Attribute\AttributeContextInterface;
use TYPO3\AccessControl\Attribute\PrincipalAttribute;

/**
 * @api
 */
final class SubjectRetrivalEvent
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