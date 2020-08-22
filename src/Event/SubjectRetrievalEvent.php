<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Event;

/*
 * This file is part of the TYPO3 project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Psr\EventDispatcher\StoppableEventInterface;
use TYPO3\AccessControl\Attribute\AttributeContextInterface;
use TYPO3\AccessControl\Attribute\AttributeInterface;

/**
 * @api
 */
final class SubjectRetrievalEvent implements StoppableEventInterface
{
    /**
     * @var AttributeContextInterface
     */
    private $context;

    /**
     * @var AttributeInterface
     */
    private $subject;

    public function __construct(?AttributeContextInterface $context = null)
    {
        $this->subject = null;
        $this->context = $context;
    }

    public function getContext(): ?AttributeContextInterface
    {
        return $this->context;
    }

    public function setSubject(AttributeInterface $subject)
    {
        $this->subject = $subject;
    }

    public function getSubject(): AttributeInterface
    {
        return $this->subject;
    }

    public function isPropagationStopped() : bool
    {
        return $this->subject !== null;
    }
}