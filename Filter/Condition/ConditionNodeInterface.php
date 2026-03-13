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
 * @author Cédric Girard <c.girard@lexik.fr>
 */
interface ConditionNodeInterface
{
    public const EXPR_AND = 'and';
    public const EXPR_OR = 'or';

    /**
     * Start a OR sub expression.
     *
     * @return static
     */
    public function orX(): static;

    /**
     * Start a AND sub expression.
     *
     * @return static
     */
    public function andX(): static;

    /**
     * Returns the parent node.
     */
    public function end(): ?self;

    /**
     * Add a field in the current expression.
     *
     * @return $this
     */
    public function field(string $name): static;

    public function getOperator(): string;

    public function getFields(): array;

    public function getChildren(): array;
}
