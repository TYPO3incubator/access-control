<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Attribute;

/*
 * This file is part of the TYPO3 project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @api
 */
final class SubjectAttribute extends QualifiedAttribute
{
    /**
     * @inheritdoc
     */
    public function __construct(PrincipalAttribute ...$principals)
    {
        parent::__construct(uniqid());

        $this->meta['principals'] = [];

        foreach ($principals as $principal) {
            $this->meta['principals'][$principal->getName()] = $principal;
        }
    }

    public function getPrincipals()
    {
        return $this->meta['principals'];
    }
}