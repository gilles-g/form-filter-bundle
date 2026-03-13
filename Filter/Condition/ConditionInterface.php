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
interface ConditionInterface
{
    /**
     * Set the name to map the condition on the ConditionBuilder instance.
     */
    public function setName(string $name): void;

    /**
     * Get condition path.
     */
    public function getName(): string;

    /**
     * Set the condition expression.
     */
    public function setExpression(string|object $expression): void;

    /**
     * Get the condition expression.
     */
    public function getExpression(): string|object;

    /**
     * Set expression parameters.
     */
    public function setParameters(array $parameters): void;

    /**
     * Get expression parameters.
     */
    public function getParameters(): array;
}
