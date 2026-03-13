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

namespace Spiriit\Bundle\FormFilterBundle\Filter;

/**
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class RelationsAliasBag
{
    private array $aliases;

    public function __construct(array $aliases = [])
    {
        $this->aliases = $aliases;
    }

    public function get(string $relation): string
    {
        return $this->aliases[$relation];
    }

    public function add(string $relation, string $alias): void
    {
        $this->aliases[$relation] = $alias;
    }

    public function has(string $relation): bool
    {
        return isset($this->aliases[$relation]);
    }

    public function count(): int
    {
        return count($this->aliases);
    }
}
