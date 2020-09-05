<?php
declare(strict_types = 1);

namespace TYPO3\AccessControl\Attribute;

/*
 * This file is part of the TYPO3 project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Exception;

/**
 * @api
 */
class AttributeNotFoundException extends Exception
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @var AttributeContextInterface
     */
    private $context;

    public function __construct(
        string $identifier, 
        AttributeContextInterface $context = null, 
        string $message = null, 
        int $code = 0, 
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->identifier = $identifier;
        $this->context = $context;
    }

    /**
     * Return the attribute identifier
     * 
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Return the attribute context
     * 
     * @return AttributeContextInterface|null
     */
    public function getContext(): ?AttributeContextInterface
    {
        return $this->identifier;
    }
}