<?php

/*
 * This file is part of the composer-write-changelogs project.
 *
 * (c) Dev Spiriit <dev@spiriit.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spiriit\Bundle\FormFilterBundle\Filter\Form\Type;

/**
 * Filter type for date range field.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class DateTimeRangeFilterType extends AbstractRangeFilterType
{
    protected function getLeftFieldType(): string
    {
        return DateTimeFilterType::class;
    }

    protected function getRightFieldType(): string
    {
        return DateTimeFilterType::class;
    }

    protected function getLeftFieldName(): string
    {
        return 'left_datetime';
    }

    protected function getRightFieldName(): string
    {
        return 'right_datetime';
    }

    protected function getLeftOptionsKey(): string
    {
        return 'left_datetime_options';
    }

    protected function getRightOptionsKey(): string
    {
        return 'right_datetime_options';
    }

    public function getBlockPrefix(): string
    {
        return 'filter_datetime_range';
    }
}
