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

use InvalidArgumentException;
use Throwable;
use TYPO3\AccessControl\Policy\Evaluation\DenyOverridesEvaluator;
use TYPO3\AccessControl\Policy\Evaluation\EvaluatorInterface;
use TYPO3\AccessControl\Policy\Evaluation\FirstApplicableEvaluator;
use TYPO3\AccessControl\Policy\Evaluation\HighestPriorityEvaluator;
use TYPO3\AccessControl\Policy\Evaluation\PermitOverridesEvaluator;
use TYPO3\AccessControl\Policy\Expression\ResolverInterface;
use Webmozart\Assert\Assert;

/**
 * @api
 */
final class PolicyFactory
{
    /**
     * Creates a policy.
     *
     * @param array $configuration Policy configuration
     * @param ResolverInterface $resolver Expression reolver to use
     * @return AbstractPolicy Policy with the given configuration
     */
    public function build(array $configuration, ResolverInterface $resolver): ?AbstractPolicy
    {
        $configuration['id'] = 'Root';

        $visited = [];
        $stack = [$configuration];
        $policies = [];

        // depth first search to build the tree starting at its leafs
        while (end($stack)) {
            $configuration = end($stack);

            $visited[$configuration['id']] = true;

            foreach ($configuration['policies'] ?? [] as $id => $policy) {
                $id = $configuration['id'] . '\\' . $id;
                if (!isset($visited[$id])) {
                    $policy['id'] = $id;
                    $stack[] = $policy;
                    continue 2;
                }
            }

            $this->validateExpression($entry['target'] ?? null, (string) $configuration['id'], $resolver);

            if (isset($configuration['rules'])) {
                $policy = new Policy(
                    (string) $configuration['id'],
                    $this->buildRules((string) $configuration['id'], $configuration['rules'], $resolver),
                    $resolver,
                    $this->buildEvaluator($configuration['algorithm'] ?? null),
                    $configuration['description'] ?? null,
                    $configuration['target'] ?? null,
                    $configuration['priority'] ?? null,
                    $this->buildObligation($configuration['obligation']['deny'] ?? null),
                    $this->buildObligation($configuration['obligation']['permit'] ?? null)
                );
            } else if (isset($configuration['policies'])) {
                $policy = new PolicySet(
                    (string) $configuration['id'],
                    $policies[$configuration['id']] ?? [],
                    $resolver,
                    $this->buildEvaluator($configuration['algorithm'] ?? null),
                    $configuration['description'] ?? null,
                    $configuration['target'] ?? null,
                    $configuration['priority'] ?? null,
                    $this->buildObligation($configuration['obligation']['deny'] ?? null),
                    $this->buildObligation($configuration['obligation']['permit'] ?? null)
                );
            }

            if (
                !$policy instanceof AbstractPolicy
                || isset($configuration['rules'])
                && isset($configuration['policies'])
            ) {
                throw new InvalidArgumentException(
                    sprintf('Unexpected policy block "%s"', $configuration['id']),
                    1561758166
                );
            }

            $previous = prev($stack);

            if (!$previous) {
                return $policy;
            }

            $policies[$previous['id']][$configuration['id']] = $policy;

            array_pop($stack);
        }

        return null;
    }

    /**
     * @return array
     */
    private function buildObligation(?array $configuration): array
    {
        $obligations = [];

        foreach ($configuration ?? [] as $operation => $arguments) {
            Assert::isArray($arguments);

            $obligations[] = new PolicyObligation(
                $operation,
                $arguments
            );
        }

        return $obligations;
    }

    /**
     * @return array
     */
    private function buildRules(string $namespace, array $configuration, ResolverInterface $resolver): array
    {
        $rules = [];

        foreach ($configuration as $id => $entry) {
            $this->validateExpression($entry['target'] ?? null, $namespace . '\\' . $id, $resolver);
            $this->validateExpression($entry['condition'] ?? null, $namespace . '\\' . $id, $resolver);

            $rules[$id] = new PolicyRule(
                $namespace . '\\' . $id,
                $resolver,
                $entry['target'] ?? null,
                $entry['condition'] ?? null,
                $entry['effect'] ?? null,
                $entry['priority'] ?? null,
                $this->buildObligation($entry['obligation']['deny'] ?? null),
                $this->buildObligation($entry['obligation']['permit'] ?? null)
            );
        }

        return $rules;
    }

    private function buildEvaluator(?string $algorithm): EvaluatorInterface
    {
        switch ($algorithm) {
            case 'denyOverrides':
                return new DenyOverridesEvaluator();
            case 'permitOverrides':
                return new PermitOverridesEvaluator();
            case 'highestPriority':
                return new HighestPriorityEvaluator();
            case 'firstApplicable':
            case null:
                return new FirstApplicableEvaluator();
        }

        throw new InvalidArgumentException(sprintf('Invalid combining algorithm "%s"', $algorithm), 1562719069);
    }

    private function validateExpression(?string $expression, string $id, ResolverInterface $resolver)
    {
        if (empty($expression)) {
            return;
        }

        try {
            $resolver->validate($expression);
        } catch (Throwable $exception) {
            throw new InvalidArgumentException(
                sprintf('Invalid target in "%s"', $id),
                1575077261,
                $exception
            );
        }
    }
}