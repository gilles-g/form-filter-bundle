<?php

/*
 * This file is part of the composer-write-changelogs project.
 *
 * (c) Dev Spiriit <dev@spiriit.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spiriit\Bundle\FormFilterBundle\Tests\Fixtures\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Spiriit\Bundle\FormFilterBundle\Attribute\Filter;
use Spiriit\Bundle\FormFilterBundle\Attribute\Filterable;
use Spiriit\Bundle\FormFilterBundle\Filter\FilterOperands;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\BooleanFilterType;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\DateFilterType;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\NumberFilterType;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\TextFilterType;

/**
 * Entity with #[Filter] attributes for testing attribute-based filtering.
 *
 * @author Spiriit <dev@spiriit.com>
 */
#[ORM\Entity]
#[Filterable(alias: 'p')]
class Product
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    protected int $id;

    #[ORM\Column]
    #[Filter(
        type: TextFilterType::class,
        label: 'Product Name',
        pattern: FilterOperands::STRING_CONTAINS
    )]
    protected string $name = '';

    #[ORM\Column(type: 'float')]
    #[Filter(
        type: NumberFilterType::class,
        operator: FilterOperands::OPERATOR_GREATER_THAN_EQUAL,
        label: 'Minimum Price'
    )]
    protected float $price = 0.0;

    #[ORM\Column(type: 'integer')]
    #[Filter(
        type: NumberFilterType::class,
        operator: FilterOperands::OPERATOR_GREATER_THAN
    )]
    protected int $stock = 0;

    #[ORM\Column(type: 'boolean')]
    #[Filter(type: BooleanFilterType::class)]
    protected bool $active = true;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Filter(type: DateFilterType::class)]
    protected ?DateTime $createdAt = null;

    #[ORM\Column(nullable: true)]
    protected ?string $description = null; // No filter attribute

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): void
    {
        $this->stock = $stock;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }
}
