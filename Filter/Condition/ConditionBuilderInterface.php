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
interface ConditionBuilderInterface
{
    /**
     * Create the root node.
     */
    public function root(string $operator): ConditionNodeInterface;

    /**
     * Add a condition to a node.
     */
    public function addCondition(ConditionInterface $condition): void;

    /**
     * Returns the root node.
     */
    public function getRoot(): ?ConditionNodeInterface;
}
