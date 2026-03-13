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

use Spiriit\Bundle\FormFilterBundle\Filter\Condition\ConditionBuilderInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event class to compute the WHERE clause from the conditions.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class ApplyFilterConditionEvent extends Event
{
    private object $queryBuilder;

    private ConditionBuilderInterface $conditionBuilder;

    public function __construct(object $queryBuilder, ConditionBuilderInterface $conditionBuilder)
    {
        $this->queryBuilder = $queryBuilder;
        $this->conditionBuilder = $conditionBuilder;
    }

    public function getConditionBuilder(): ConditionBuilderInterface
    {
        return $this->conditionBuilder;
    }

    public function getQueryBuilder(): object
    {
        return $this->queryBuilder;
    }
}
