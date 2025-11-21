# Generate Form Filter Command

The bundle provides a CLI command to automatically generate form filter types from Doctrine entities.

## Command

```bash
php bin/console spiriit:form-filter:generate <entity-class>
```

## Usage

### Basic Usage

Generate a form filter type from an entity:

```bash
php bin/console spiriit:form-filter:generate "App\Entity\User"
```

This will:
1. Analyze the entity's properties using Doctrine metadata
2. Generate a filter type class with appropriate field types
3. Save the file in the `Form\Filter` namespace (replacing `Entity` in the original namespace)

### Custom Output Directory

You can specify a custom output directory:

```bash
php bin/console spiriit:form-filter:generate "App\Entity\User" --output-dir=src/Form/Filter
```

## Field Type Mapping

The command automatically maps Doctrine field types to appropriate filter types:

| Doctrine Type | Filter Type |
|--------------|-------------|
| string, text | TextFilterType |
| integer, int, smallint, bigint | NumberFilterType |
| decimal, float | NumberFilterType |
| boolean, bool | BooleanFilterType |
| date | DateFilterType |
| datetime, datetimetz, time | DateTimeFilterType |
| ManyToOne, OneToOne associations | EntityFilterType |

## Example

Given this entity:

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Article
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\Column(type: 'boolean')]
    private bool $published;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $author;
}
```

Running:

```bash
php bin/console spiriit:form-filter:generate "App\Entity\Article"
```

Will generate `App\Form\Filter\ArticleFilterType.php`:

```php
<?php

namespace App\Form\Filter;

use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\BooleanFilterType;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\DateTimeFilterType;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\EntityFilterType;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\TextFilterType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ArticleFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('title', TextFilterType::class);
        $builder->add('content', TextFilterType::class);
        $builder->add('published', BooleanFilterType::class);
        $builder->add('createdAt', DateTimeFilterType::class);
        $builder->add('author', EntityFilterType::class, [
            'class' => User::class,
        ]);
    }
}
```

## Notes

- The command skips ID fields automatically
- You can customize the generated filter type after creation
- The command will ask for confirmation if the output file already exists
- The entity class must be a valid Doctrine entity with metadata
