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

namespace Spiriit\Bundle\FormFilterBundle\Event;

use Spiriit\Bundle\FormFilterBundle\Filter\Query\QueryInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Get alias and expression builder for filter builder
 *
 * @author Jeremy Barthe <j.barthe@lexik.fr>
 */
class PrepareEvent extends Event
{
    private object $queryBuilder;

    private ?QueryInterface $filterQuery = null;

    /**
     * Construct
     */
    public function __construct(object $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * Get query builder
     */
    public function getQueryBuilder(): object
    {
        return $this->queryBuilder;
    }

    /**
     * Set filter query
     */
    public function setFilterQuery(QueryInterface $filterQuery): void
    {
        $this->filterQuery = $filterQuery;
    }

    /**
     * Get filter query
     *
     * @return QueryInterface
     */
    public function getFilterQuery(): ?QueryInterface
    {
        return $this->filterQuery;
    }
}
