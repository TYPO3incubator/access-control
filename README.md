# TYPO3 Access Control

[![Build](https://img.shields.io/travis/com/TYPO3-Initiatives/security/master.svg)](https://travis-ci.com/typo3-initiatives/access-control)
[![Coverage](https://img.shields.io/codacy/coverage/a3ba86a8a97846e9a8bca68975f22c66/master.svg)](https://app.codacy.com/project/typo3-initiatives/access-control/dashboard)
[![Code Quality](https://img.shields.io/codacy/grade/a3ba86a8a97846e9a8bca68975f22c66/master.svg)](https://app.codacy.com/project/typo3-initiatives/access-control/dashboard)

## Installation

Use composer to install this extension in your project:

```bash
composer config repositories.access-control git https://github.com/typo3-initiatives/access-control
composer require typo3/access-control
```

## Concepts

Access rights are granted to users through the use of policies. The underlying model is known as [attribute-based access control](https://en.wikipedia.org/wiki/attribute-based_access_control) (ABAC). It makes use of boolean expressions which decide whether an access request is granted or not. Such a request typically contains the *resource*, *action*, *subject* and *environment* attributes. This extension implements a lightweight policy language and evaluation framework based on [Jiang, Hao & Bouabdallah, Ahmed (2017)](https://www.researchgate.net/publication/325873238).

The policy structure consists of *policy sets*, *policies* and *rules*. A *policy set* is a set of *policies* which in turn has a set of *rules*. Because not all policies are relevant to a given request every element includes the notion of a *target*. It determines whether a policy is applicable to a request by setting constraints on attributes using boolean expressions.

A policy is *applicable* if the access request satisfies the target. If so, its childrend are evaluated and the results returned by those children are combined using a combining algorithm. Otherwise, the policy is skipped without further examining its children and returns a *not applicable* *decision*.

The *rule* is the fundamental unit that can generate a conclusive *decision*. The *condition* of a *rule* is a more complex boolean expression that refines the applicability beyond the predicates specified by its *target*, and is optional. If a request satisfies both the *target* and *condition* of a *rule*, then the *rule* is applicable to the request and its *eﬀect* is returned as its *decision*. Otherwise, *not applicable* is returned.

Each *rule*, *policy* or *policy set* has an unique identifier and *obligations* which is used to specify the operations which should be performed after granting or denying an access request.

## Attributes

A request typically contains the following attributes:

| Attribute | Description |
| --- | --- |
| `resource` | Is an entity to be protected from unauthorized use. The *resource* is directly provided by the access request. See also `TYPO3\AccessControl\Attribute\ResourceAttribute`. |
| `subject` | Represents the entity requesting to perform an operation upon the *resource*. It is provided indirectly through the given context of the policy decision point and can not modifed or set by the access request directly.  See also `TYPO3\AccessControl\Attribute\SubjectAttribute`. |
| `action` | The operations to be performed on the *resource*. Like the *resource* it is also provided by the access request. See also `TYPO3\AccessControl\Attribute\ActionAttribute`. |

To define your own attributes, you must derive from one of the corresponding classes:

```php
namespace App\Security\AccessControl\Attribute;

use TYPO3\AccessControl\Attribute\PrincipalAttribute;

class RoleAttribute extends PrincipalAttribute
{
  public function __construct(string $identifier)
  {
    parent::__construct($identifier);
  }
}
```

## Expressions

Expressions are used to decied whether a policy is applicable to a request or not. Therefore a so called expression resolver has to be implemented. For example, by using the expression language component:

```php
namespace App\Security\AccessControl\Expression;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use TYPO3\AccessControl\Expression\ResolverInterface;

class ExpressionLanguageResolver implements ResolverInterface
{
  private $expressionLanguage;

  public function __construct()
  {
    $this->expressionLanguage = new ExpressionLanguage();
    // register a custom function `hasAuthority`
    $this->expressionLanguage->register(
      'hasAuthority', function () {
        // not implemented, we only use the evaluator
      },
      function ($variables, ...$arguments) {
        if (count($arguments) == 1) {
          // checks if the subject has the given principal
          return isset($variables['subject']->principals[$arguments[0]]);
        }
        return false;
      }
    );
  }

  public function validate(string $expression): void
  {
    // only allow the attributes `subject`, `resource` and `action`
    $this->expressionLanguage->parse($expression, ['subject', 'resource', 'action']);
  }

  public function evaluate(string $expression, array $attributes): bool
  {
    return $this->expressionLanguage->evaluate($expression, $attributes);
  }
}
```

## Policies

Policies have to be defined declaratively. For example, by using YAML and the policy factory:

```yaml
---
Policy:
  description: 'Root policy set.'
  algorithm: highestPriority
  policies:
    Admin:
      target: 'hasAuthority("role", "ADMIN")'
      description: 'Administrator policy'
      priority: 100
      rules:
        -
          effect: permit
    Default:
      description: 'Deny everything per default.'
      rules:
        -
          obligation:
            deny:
              Feedback: ['Access denied.']
```

```php
use App\Security\AccessControl\Expression\ExpressionLanguageResolver;
use Symfony\Component\Yaml\Parser;
use TYPO3\AccessControl\Policy\PolicyFactory;

$resolver = new ExpressionLanguageResolver();
$factory = new PolicyFactory();
$parser = new Parser();

$policy = $factory->build(
  Yaml::parseFile('/path/to/policies.yaml'),
  $resolver
);
```

A **policy set** is a set of *policy sets* and *policies*. It has the following configuration fields:

| Field | Description |
| --- | --- |
| `description` | Optional description of the policy set. |
| `target` | Optional boolean expression indicating the *resource*, *action*, *subject* and *environment attributes* to which the *policy set* is applied. Default is `true`. |
| `alogrithm` | Optional name of a *combining algorithm* to compute the final decision according to the results returned by its child policies, either `denyOverride`, `permitOverride`, `firstApplicable` or `highestPriority`. Default is `firstApplicable`. |
| `priority` | Optional number indicating the weight of the *policy set* when its decision conﬂicts with other policies under the `highestPriority` algorithm. Default is `1`. |
| `obligation` | Optional actions to take in case a particular conclusive decision (*permit* or *deny*) is reached. |
| `policies` | Required set of child policies (*policy sets* and *policies*). |

With configuration fields similar to a *policy set* a **policy** is a set of *rules*:

| Field | Description |
| --- | --- |
| `description` | Optional description of the policy. |
| `target` | Optional [boolean expression](https://symfony.com/doc/current/components/expression_language/syntax.html) indicating the *resource*, *action*, *subject* and *environment attributes* to which the *policy* is applied. Default is `true`. |
| `alogrithm` | Optional name of a *combining algorithm* to compute the final decision according to the results returned by its child rules, either `denyOverride`, `permitOverride`, `firstApplicable` or `highestPriority`. Default is `firstApplicable`. |
| `priority` | Optional number indicating the weight of the *policy* when its decision conﬂicts with other policies under the `highestPriority` algorithm. Default is `1`. |
| `obligation` | Optional actions to take in case a particular conclusive decision (*permit* or *deny*) is reached. |
| `rules` | Required set of child *rules*. |

Unlike a *policy set* or a *policy*, a **rule** does not contain any leaf nodes:

| Field | Description |
| --- | --- |
| `target` | Optional [boolean expression](https://symfony.com/doc/current/components/expression_language/syntax.html) indicating the *resource*, *action*, *subject* and *environment attributes* to which the *policy* is applied. Default is `true`. |
| `effect` | Optional returned decision when the rule is applied, either `permit` or `deny`. Default is `deny`. |
| `condition` | Optional [boolean expression](https://symfony.com/doc/current/components/expression_language/syntax.html) that specifies the condition for applying the rule. In comparison to a `target`, a `condition` is typically more complex. If either the `target` or the `condition` is not satisfied, a *not applicable* would be taken as the result instead of the specified `effect`. Default is `true`. |
| `priority` | Optional number indicating the weight of the *rule* when its decision conﬂicts with other rules under the `highestPriority` algorithm. Default is `1`. |
| `obligation` | Optional actions to take in case a particular conclusive decision (*permit* or *deny*) is reached. |

Policies may conflict and produce different *decisions* for the same request. To resolve this four kinds of
**combining algorithms** are provided. Each algorithm represents a different way for combining multiple local *decisions* into a single global *decision*:

| Algorithm | Description |
| --- | --- |
| `permitOverrides` | Returns *permit* if any *decision* evaluates to *permit* and returns *deny* if all *decisions* evaluate to *deny*. |
| `denyOverrides` | Returns *deny* if any *decision* evaluates to *deny* and returns *permit* if all *decisions* evaluate to *permit*. |
| `firstApplicable` | Returns the first *decision* that evaluates to either of *permit* or *deny*. |
| `highestPriority` | Returns the highest priority *decision* that evaluates to either of *permit* or *deny*. If there are multiple equally highest priority *decisions* that conflict, then *deny overrides* algorithm would be applied among those highest priority *decisions*. |

Please note that for all of these *combining algorithms*, *not applicable* is returned if not any of the children is applicable.

### Authorisation

To perform an access request the *policy decision point* has to be used. It evaluates all policies and returns a *decision* either of *permit*, *deny* or *not applicable*:

```php
use App\Security\AccessControl\Attribute\ActionAttribute;
use App\Security\AccessControl\Attribute\ResourceAttribute;
use Symfony\Component\EventDispatcher\EventDispatcher;
use TYPO3\AccessControl\Policy\PolicyDecisionPoint;
use TYPO3\AccessControl\Policy\PolicyInformationPoint;

$dispatcher = new EventDispatcher();

// creeates an policy information point
$policyInformationPoint = new PolicyInformationPoint(
  $dispatcher
);

// creates a policy decision point
$policyDecisionPoint = new PolicyDecisionPoint(
  $dispatcher,
  $policy,
  $policyInformationPoint
);

// perform an authorization request
$policyDecision = $policyDecisionPoint->authorize(
  [
    // concrete resource to access
    'resource' => new ResourceAttribute('identifier'),
    // concrete action on resource
    'action' => new ActionAttribute()
  ]
);

if (!$policyDecision->isApplicable()) {
  // access request is not applicable
}

// process determining policy rule
$determinigRule = $policyDecision->getRule();

foreach ($policyDecision->getObligations() as $obligation) {
  // process obligations
}

if ($policyDecision->getValue() === PolicyDecision::PERMIT)
  // access is granted
}

// access is denied otherwise
```

To receive all operations which should be performed after denying or granting an access request the event `\TYPO3\AccessControl\Event\PolicyDecisionEvent` has to be used:

```php
namespace App\Security\AccessControl\EventListener;

use TYPO3\AccessControl\Event\PolicyDecisionEvent;

class PolicyDecisionListener
{
    public function __invoke(PolicyDecisionEvent $event)
    {
        // ...
    }
}
```

To provide additional data for an attribute before an access request the event `\TYPO3\AccessControl\Event\AttributeRetrivalEvent` can be used:

```php
namespace App\Security\AccessControl\EventListener;

use TYPO3\AccessControl\Event\AttributeRetrivalEvent;

class AttributeRetrivalListener
{
    public function __invoke(AttributeRetrivalEvent $event)
    {
        // ...
    }
}
```

To provide principals for the subject attribute the separate event `\TYPO3\AccessControl\Event\SubjectRetrivalEvent` has to be used:

```php
namespace App\Security\AccessControl\EventListener;

use TYPO3\AccessControl\Event\SubjectRetrivalEvent;

class SubjectRetrivalListener
{
    public function __invoke(SubjectRetrivalEvent $event)
    {
        // ...
    }
}
```

### Design Principals

Whenever possible the authorization logic should be part of a policy. Thus its auditable and changeable. For reasons of the performance or complexity it might be not possible. Then it's recommended to extend the expression language with a custom function.

## Development

Development for this extension is happening as part of the [TYPO3 persistence initiative](https://typo3.org/community/teams/typo3-development/initiatives/persistence/).
