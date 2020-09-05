<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Policy;

/*
 * This file is part of the TYPO3 project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ArrayAccess;
use IteratorAggregate;
use TYPO3\AccessControl\Exception\NotSupportedMethodException;
use TYPO3\AccessControl\Policy\Evaluation\EvaluableInterface;
use TYPO3\AccessControl\Policy\Expression\ResolverInterface;
use Webmozart\Assert\Assert;

/**
 * @internal
 */
abstract class AbstractPolicy implements EvaluableInterface, IteratorAggregate, ArrayAccess
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var ResolverInterface
     */
    protected $resolver;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $target;

    /**
     * @var int
     */
    protected $priority;

    /**
     * @var array
     */
    protected $denyObligations;

    /**
     * @var array
     */
    protected $permitObligations;

    public function __construct(
        string $id,
        ResolverInterface $resolver,
        ?string $description,
        ?string $target,
        ?int $priority,
        ?array $denyObligations,
        ?array $permitObligations
    ) {
        Assert::stringNotEmpty($id, '$id must not be empty');
        Assert::allIsInstanceOf($denyObligations ?? [], PolicyObligation::class);
        Assert::allIsInstanceOf($permitObligations ?? [], PolicyObligation::class);

        $this->id = $id;
        $this->resolver = $resolver;
        $this->description = $description;
        $this->target = $target;
        $this->priority = $priority ?? 1;
        $this->denyObligations = $denyObligations ?? [];
        $this->permitObligations = $permitObligations ?? [];
    }

    public function offsetSet($offset, $value): void
    {
        throw new NotSupportedMethodException();
    }
    public function offsetUnset($offset): void
    {
        throw new NotSupportedMethodException();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @return array
     */
    public function getDenyObligations(): array
    {
        return $this->denyObligations;
    }

    /**
     * @return array
     */
    public function getPermitObligations(): array
    {
        return $this->permitObligations;
    }
}