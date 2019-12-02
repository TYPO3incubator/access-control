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
abstract class AbstractAttribute
{
    /**
     * @var array
     */
    protected $meta = [];

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (!isset($this->meta[$name])) {
            throw new \RuntimeException(sprintf('Unknown property "%s"', $name), 1572800990);
        }

        return $this->meta[$name];
    }
}
