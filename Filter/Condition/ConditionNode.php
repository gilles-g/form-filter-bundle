<?php

declare(strict_types=1);

/*
 * This file is part of the composer-write-changelogs project.
 *
 * (c) Dev Spiriit <dev@spiriit.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spiriit\Bundle\FormFilterBundle\Filter\Condition;

/**
 * Define the operator to use for a list of fields.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class ConditionNode implements ConditionNodeInterface
{
    private string $operator;

    private ?ConditionNodeInterface $parent;

    private array $children;

    private array $fields;

    public function __construct(string $operator, ?ConditionNodeInterface $parent = null)
    {
        $this->operator = $operator;
        $this->parent = $parent;
        $this->children = [];
        $this->fields = [];
    }

    /**
     * {@inheritDoc}
     */
    public function orX(): static
    {
        $node = new static(self::EXPR_OR, $this);

        $this->children[] = $node;

        return $node;
    }

    /**
     * {@inheritDoc}
     */
    public function andX(): static
    {
        $node = new static(self::EXPR_AND, $this);

        $this->children[] = $node;

        return $node;
    }

    /**
     * {@inheritDoc}
     */
    public function end(): ?ConditionNodeInterface
    {
        return $this->parent;
    }

    /**
     * {@inheritDoc}
     */
    public function field(string $name): static
    {
        $this->fields[$name] = null;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * {@inheritDoc}
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * {@inheritDoc}
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * Set the condition for the given field name.
     */
    public function setCondition(string $name, ConditionInterface $condition): bool
    {
        if (array_key_exists($name, $this->fields)) {
            $this->fields[$name] = $condition;

            return true;
        }

        $i = 0;
        $end = count($this->children);
        $set = false;

        while ($i < $end && !$set) {
            $set = $this->children[$i]->setCondition($name, $condition);
            $i++;
        }

        return $set;
    }
}
