[8] Attribute-Based Filters (PHP 8+)
====================================

SpiriitFormFilterBundle provides a modern, attribute-based approach for defining filters that reduces boilerplate code and keeps filter configuration close to your domain models.

i. Introduction
---------------

### Traditional FormType Approach

With the traditional approach, you create a FormType class and manually add each filter field:

```php
<?php

namespace App\Filter;

use Spiriit\Bundle\FormFilterBundle\Filter\FilterOperands;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ProductFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', Filters\TextFilterType::class, [
            'condition_pattern' => FilterOperands::STRING_CONTAINS,
            'label' => 'Product Name',
        ]);
        $builder->add('price', Filters\NumberFilterType::class, [
            'condition_operator' => FilterOperands::OPERATOR_GREATER_THAN_EQUAL,
            'label' => 'Minimum Price',
        ]);
        $builder->add('stock', Filters\NumberFilterType::class, [
            'condition_operator' => FilterOperands::OPERATOR_GREATER_THAN,
        ]);
        $builder->add('active', Filters\BooleanFilterType::class);
        $builder->add('createdAt', Filters\DateFilterType::class);
    }
}
```

### Attribute-Based Approach (PHP 8+)

With attributes, you define filters directly on your entity or DTO properties:

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Spiriit\Bundle\FormFilterBundle\Attribute\Filter;
use Spiriit\Bundle\FormFilterBundle\Attribute\Filterable;
use Spiriit\Bundle\FormFilterBundle\Filter\FilterOperands;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type as Filters;

#[ORM\Entity]
#[Filterable]
class Product
{
    #[ORM\Column]
    #[Filter(
        type: Filters\TextFilterType::class,
        label: 'Product Name',
        pattern: FilterOperands::STRING_CONTAINS
    )]
    private string $name;

    #[ORM\Column(type: 'float')]
    #[Filter(
        type: Filters\NumberFilterType::class,
        label: 'Minimum Price',
        operator: FilterOperands::OPERATOR_GREATER_THAN_EQUAL
    )]
    private float $price;

    #[ORM\Column(type: 'integer')]
    #[Filter(operator: FilterOperands::OPERATOR_GREATER_THAN)]
    private int $stock;

    #[ORM\Column(type: 'boolean')]
    #[Filter]
    private bool $active;

    #[ORM\Column(type: 'datetime')]
    #[Filter]
    private \DateTime $createdAt;

    #[ORM\Column]
    private string $description; // No #[Filter] = not filterable
}
```

Then create a minimal filter type:

```php
<?php

namespace App\Filter;

use App\Entity\Product;
use Spiriit\Bundle\FormFilterBundle\Attribute\AbstractAutoFilterType;

class ProductFilterType extends AbstractAutoFilterType
{
    protected function getFilterableClass(): string
    {
        return Product::class;
    }
}
```

ii. Available Attributes
------------------------

### #[Filterable]

Marks a class as having filterable properties. Optional but provides additional control.

| Parameter | Type | Description |
|-----------|------|-------------|
| `include` | `string[]` | Only include these properties (even if they have #[Filter]) |
| `exclude` | `string[]` | Exclude these properties |
| `alias` | `?string` | Default query alias |

Example:
```php
#[Filterable(exclude: ['internalField'], alias: 'p')]
class Product { ... }
```

### #[Filter]

Configures a filter for a property.

| Parameter | Type | Description |
|-----------|------|-------------|
| `type` | `?string` | Filter form type class (auto-detected if not specified) |
| `name` | `?string` | Field name in form (defaults to property name) |
| `label` | `?string` | Form field label |
| `options` | `array` | Additional form type options |
| `operator` | `?string` | Numeric comparison operator (e.g., `FilterOperands::OPERATOR_EQUAL`) |
| `pattern` | `?int` | String comparison pattern (e.g., `FilterOperands::STRING_CONTAINS`) |
| `required` | `bool` | Whether the field is required (default: `false`) |
| `fieldName` | `?string` | Custom field name for Doctrine query |

iii. Type Auto-Detection
------------------------

When `type` is not specified in `#[Filter]`, the system auto-detects the filter type based on the property's PHP type:

| PHP Type | Filter Type |
|----------|-------------|
| `string` | `TextFilterType` |
| `int`, `integer` | `NumberFilterType` |
| `float`, `double` | `NumberFilterType` |
| `bool`, `boolean` | `BooleanFilterType` |
| `DateTime` | `DateTimeFilterType` |
| `DateTimeImmutable` | `DateTimeFilterType` |
| `DateTimeInterface` | `DateTimeFilterType` |

Example with auto-detection:
```php
class SearchDto
{
    #[Filter] // Auto-detects TextFilterType
    public string $keyword;

    #[Filter] // Auto-detects NumberFilterType
    public int $quantity;

    #[Filter] // Auto-detects BooleanFilterType
    public bool $isActive;
}
```

iv. Using the Services Directly
-------------------------------

You can also use the services directly for more control:

### FilterableReader

Reads attribute metadata from classes:

```php
use Spiriit\Bundle\FormFilterBundle\Attribute\FilterableReader;

$reader = new FilterableReader();

// Check if class has #[Filterable]
$isFilterable = $reader->isFilterable(Product::class);

// Get all #[Filter] attributes
$filters = $reader->getFilters(Product::class);

// Get specific property's filter
$filter = $reader->getFilter(Product::class, 'name');

// Get property's PHP type
$type = $reader->getPropertyType(Product::class, 'price'); // 'float'
```

### AutoFilterTypeFactory

Builds form fields from attributes:

```php
use Spiriit\Bundle\FormFilterBundle\Attribute\AutoFilterTypeFactory;

$factory = $container->get(AutoFilterTypeFactory::class);

// Get configuration array (useful for inspection/debugging)
$config = $factory->getFilterConfiguration(Product::class);

// Build fields directly onto a FormBuilder
$factory->buildFilters($builder, Product::class);
```

v. Extending AbstractAutoFilterType
-----------------------------------

You can add additional fields or customize behavior:

```php
use Spiriit\Bundle\FormFilterBundle\Attribute\AbstractAutoFilterType;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Symfony\Component\Form\FormBuilderInterface;

class ProductFilterType extends AbstractAutoFilterType
{
    protected function getFilterableClass(): string
    {
        return Product::class;
    }

    protected function buildAdditionalFields(FormBuilderInterface $builder, array $options): void
    {
        // Add fields that aren't defined via attributes
        $builder->add('category', Filters\EntityFilterType::class, [
            'class' => Category::class,
        ]);
    }
}
```

vi. Using with DTOs (Data Transfer Objects)
-------------------------------------------

Attributes work great with DTOs for DDD-friendly filter definitions:

```php
namespace App\Application\Query;

use Spiriit\Bundle\FormFilterBundle\Attribute\Filter;
use Spiriit\Bundle\FormFilterBundle\Attribute\Filterable;
use Spiriit\Bundle\FormFilterBundle\Filter\FilterOperands;

#[Filterable]
class ProductSearchCriteria
{
    #[Filter(pattern: FilterOperands::STRING_CONTAINS)]
    public string $name = '';

    #[Filter(operator: FilterOperands::OPERATOR_GREATER_THAN_EQUAL)]
    public ?float $minPrice = null;

    #[Filter(operator: FilterOperands::OPERATOR_LOWER_THAN_EQUAL)]
    public ?float $maxPrice = null;

    #[Filter]
    public ?bool $active = null;
}
```

Use in a filter type:
```php
class ProductSearchFilterType extends AbstractAutoFilterType
{
    protected function getFilterableClass(): string
    {
        return ProductSearchCriteria::class;
    }
}
```

vii. Comparison: Traditional vs Attribute-Based
-----------------------------------------------

| Aspect | Traditional FormType | Attribute-Based |
|--------|---------------------|-----------------|
| **Boilerplate** | High - repeat field definitions | Low - minimal filter type |
| **Maintainability** | Changes in two places (entity + form) | Single source of truth |
| **Type Safety** | Manual type matching | Auto-detection available |
| **Flexibility** | Full control | Can extend for custom needs |
| **IDE Support** | Good | Excellent with attributes |
| **DDD Compatibility** | Requires careful organization | Natural fit with DTOs |
| **Symfony 6/7/8** | ✓ | ✓ (PHP 8.1+) |
| **Learning Curve** | Familiar to Symfony developers | Minimal learning required |

### When to Use Each Approach

**Use Traditional FormType when:**
- You need complex, dynamic filter logic
- Filter fields don't map directly to entity properties
- You're working with legacy code

**Use Attribute-Based when:**
- Filters map directly to entity/DTO properties
- You want to reduce boilerplate
- You prefer keeping configuration close to domain
- You're building a DDD application

viii. Service Configuration
---------------------------

The attribute-based services are registered automatically when using the bundle. They're available for autowiring:

```php
use Spiriit\Bundle\FormFilterBundle\Attribute\AutoFilterTypeFactory;
use Spiriit\Bundle\FormFilterBundle\Attribute\FilterableReader;

class MyService
{
    public function __construct(
        private FilterableReader $reader,
        private AutoFilterTypeFactory $factory,
    ) {}
}
```

Or via the container:
```yaml
services:
    App\Filter\ProductFilterType:
        arguments:
            - '@Spiriit\Bundle\FormFilterBundle\Attribute\AutoFilterTypeFactory'
```

***

Next: [Back to Index](index.md)
