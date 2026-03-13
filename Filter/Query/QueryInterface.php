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

namespace Spiriit\Bundle\FormFilterBundle\Filter\Query;

use Spiriit\Bundle\FormFilterBundle\Filter\Condition\ConditionInterface;

/**
 * @author Jeremy Barthe <j.barthe@lexik.fr>
 */
interface QueryInterface
{
    /**
     * Get query builder (of ORM, DBAL, ODM, Propel, etc.).
     */
    public function getQueryBuilder(): object;

    /**
     * Return a part name of filter events (ex: orm, dbal, propel, etc.).
     */
    public function getEventPartName(): string;

    public function createCondition(string|object $expression, array $parameters = []): ConditionInterface;

    /**
     * Get root alias.
     */
    public function getRootAlias(): string;

    public function hasJoinAlias(string $joinAlias): bool;
}
