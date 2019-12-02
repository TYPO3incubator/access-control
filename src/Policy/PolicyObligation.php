<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Policy;

/*
 * This file is part of the TYPO3 project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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