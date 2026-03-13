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

namespace Spiriit\Bundle\FormFilterBundle\Filter\Doctrine\Expression;

use DateTime;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\Query\Expr\Literal;
use InvalidArgumentException;
use Spiriit\Bundle\FormFilterBundle\Filter\FilterOperands;

abstract class ExpressionBuilder
{
    public const SQL_DATE = 'Y-m-d';
    public const SQL_DATE_TIME = 'Y-m-d H:i:s';

    protected mixed $expr;

    protected bool $forceCaseInsensitivity;

    protected ?string $encoding;

    /**
     * Get expression object.
     */
    public function expr(): mixed
    {
        return $this->expr;
    }

    public function __construct(bool $forceCaseInsensitivity, ?string $encoding = null)
    {
        $this->forceCaseInsensitivity = $forceCaseInsensitivity;
        $this->encoding = $encoding;
    }

    /**
     * Returns between expression if min and max not null
     * Returns lte expression if max is null
     * Returns gte expression if min is null
     */
    public function inRange(string $field, $min, $max): Comparison|string|null
    {
        if (!$min && !$max) {
            return null;
        }
        if (null === $min) {
            // $max exists
            return $this->expr()->lte($field, (float) $max);
        }

        if (null === $max) {
            // $min exists
            return $this->expr()->gte($field, (float) $min);
        }

        // both $min and $max exists
        return $this->between($field, (float) $min, (float) $max);
    }

    /**
     * Creates BETWEEN() function with the given argument.
     */
    public function between(string $field, float|int $min, float|int $max): string
    {
        return $field . ' BETWEEN ' . $min . ' AND ' . $max;
    }

    /**
     * Returns between expression if min and max not null
     * Returns lte expression if max is null
     * Returns gte expression if min is null
     */
    public function dateInRange(string $field, ?DateTime $min = null, ?DateTime $max = null): mixed
    {
        if (!$min && !$max) {
            return null;
        }

        $min = $this->convertToSqlDate($min);
        $max = $this->convertToSqlDate($max, true);
        if (null === $min) {
            // $max exists
            return $this->expr()->lte($field, $max);
        }

        if (null === $max) {
            // $min exists
            return $this->expr()->gte($field, $min);
        }

        // both $min and $max exists
        return $this->expr()->andX(
            $this->expr()->lte($field, $max),
            $this->expr()->gte($field, $min)
        );
    }

    /**
     * Returns between expression if min and max not null
     * Returns lte expression if max is null
     * Returns gte expression if min is null
     */
    public function dateTimeInRange(string|DateTime $value, string|DateTime|null $min = null, string|DateTime|null $max = null): mixed
    {
        if (!$min && !$max) {
            return null;
        }

        $value = $this->convertToSqlDateTime($value);
        $min = $this->convertToSqlDateTime($min);
        $max = $this->convertToSqlDateTime($max);

        if (!$max && !$min) {
            return null;
        }

        if ($min === null) {
            $findExpression = $this->expr()->lte($value, $max);
        } elseif ($max === null) {
            $findExpression = $this->expr()->gte($value, $min);
        } else {
            $findExpression = $this->expr()->andX(
                $this->expr()->lte($value, $max),
                $this->expr()->gte($value, $min)
            );
        }

        return $findExpression;
    }

    /**
     * Get string like expression.
     */
    public function stringLike(string $field, string $value, int $type = FilterOperands::STRING_CONTAINS): mixed
    {
        $value = $this->convertTypeToMask($value, $type);

        return $this->expr()->like(
            $this->forceCaseInsensitivity ? $this->expr()->lower($field) : $field,
            $this->expr()->literal($value)
        );
    }

    /**
     * Normalize DateTime boundary.
     */
    protected function convertToSqlDate(?DateTime $date, bool $isMax = false): mixed
    {
        if (!$date instanceof DateTime) {
            return null;
        }

        $copy = clone $date;

        if ($isMax) {
            $copy->modify('+1 day -1 second');
        }

        return $this->expr()->literal($copy->format(self::SQL_DATE_TIME));
    }

    /**
     * Normalize date time boundary.
     */
    protected function convertToSqlDateTime(DateTime|string|null $date): mixed
    {
        if ($date instanceof DateTime) {
            return $this->expr()->literal($date->format(self::SQL_DATE_TIME));
        }

        return $date;
    }

    /**
     * Prepare value for like operation.
     *
     * @throws InvalidArgumentException
     */
    protected function convertTypeToMask(string $value, int $type): string
    {
        if ($this->forceCaseInsensitivity) {
            $value = $this->encoding ? mb_strtolower($value, $this->encoding) : mb_strtolower($value);
        }

        switch ($type) {
            case FilterOperands::STRING_STARTS:
                $value .= '%';
                break;

            case FilterOperands::STRING_ENDS:
                $value = '%' . $value;
                break;

            case FilterOperands::STRING_CONTAINS:
                $value = '%' . $value . '%';
                break;

            case FilterOperands::STRING_EQUALS:
                break;

            default:
                throw new InvalidArgumentException('Wrong type constant in string like expression mapper');
        }

        return $value;
    }
}
