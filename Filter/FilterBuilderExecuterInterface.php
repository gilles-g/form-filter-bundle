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

use Closure;
use Spiriit\Bundle\FormFilterBundle\Filter\Query\QueryInterface;

/**
 * @author Cédric Girard <c.girard@lexik.fr>
 */
interface FilterBuilderExecuterInterface
{
    /**
     * Add a join.
     */
    public function addOnce(string $join, string $alias, ?Closure $callback = null): mixed;

    public function getAlias(): ?string;

    public function getParts(): RelationsAliasBag;

    public function getFilterQuery(): QueryInterface;
}
