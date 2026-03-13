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
class FilterBuilderExecuter implements FilterBuilderExecuterInterface
{
    protected QueryInterface $filterQuery;

    protected ?string $alias;

    protected RelationsAliasBag $parts;

    public function __construct(QueryInterface $filterQuery, ?string $alias, RelationsAliasBag $parts)
    {
        $this->filterQuery = $filterQuery;
        $this->alias = $alias;
        $this->parts = $parts;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias(): ?string
    {
        return $this->alias;
    }

    /**
     * {@inheritdoc}
     */
    public function getParts(): RelationsAliasBag
    {
        return $this->parts;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterQuery(): QueryInterface
    {
        return $this->filterQuery;
    }

    /**
     * {@inheritdoc}
     */
    public function addOnce(string $join, string $alias, ?Closure $callback = null): mixed
    {
        if ($this->parts->has($join)) {
            return null;
        }

        $this->parts->add($join, $alias);

        if (!$callback instanceof Closure) {
            return null;
        }

        return $callback($this->filterQuery->getQueryBuilder(), $this->alias, $alias, $this->filterQuery->getExpr());
    }
}
