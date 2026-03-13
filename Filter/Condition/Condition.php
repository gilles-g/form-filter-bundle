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
 * Represent a filter condition to be added on a query builder.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class Condition implements ConditionInterface
{
    public string $name = '';

    private string|object $expression;

    /**
     * array(
     *     'param_name_1' => $value,
     *     'param_nema_2  => array($value, $type),
     * )
     */
    private array $parameters;

    public function __construct(string|object $expression, array $parameters = [])
    {
        $this->expression = $expression;
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setExpression(string|object $expression): void
    {
        $this->expression = $expression;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpression(): string|object
    {
        return $this->expression;
    }

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
