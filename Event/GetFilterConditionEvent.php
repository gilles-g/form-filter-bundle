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

use Spiriit\Bundle\FormFilterBundle\Filter\Condition\Condition;
use Spiriit\Bundle\FormFilterBundle\Filter\Condition\ConditionInterface;
use Spiriit\Bundle\FormFilterBundle\Filter\Query\QueryInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @author Jeremy Barthe <j.barthe@lexik.fr>
 */
class GetFilterConditionEvent extends Event
{
    private QueryInterface $filterQuery;

    private string $field;

    private array $values;

    private ?Condition $condition = null;

    public function __construct(QueryInterface $filterQuery, string $field, array $values)
    {
        $this->filterQuery = $filterQuery;
        $this->field = $field;
        $this->values = $values;
    }

    public function getFilterQuery(): QueryInterface
    {
        return $this->filterQuery;
    }

    public function getQueryBuilder(): object
    {
        return $this->filterQuery->getQueryBuilder();
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function setCondition(string|object $expression, array $parameters = []): void
    {
        $this->condition = new Condition($expression, $parameters);
    }

    public function getCondition(): ?ConditionInterface
    {
        return $this->condition;
    }
}
