<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Policy;

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

use Webmozart\Assert\Assert;

/**
 * @internal
 */
final class PolicyObligation
{
    /**
     * @var string
     */
    private $operation;

    /**
     * @var string[]
     */
    private $arguments;

    public function __construct(string $operation, array $arguments = [])
    {
        Assert::stringNotEmpty($operation);

        $this->operation = $operation;
        $this->arguments = $arguments;
    }

    public function getOperation(): string
    {
        return $this->operation;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }
}