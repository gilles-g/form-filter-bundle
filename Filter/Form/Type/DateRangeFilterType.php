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
class DateRangeFilterType extends AbstractRangeFilterType
{
    protected function getLeftFieldType(): string
    {
        return DateFilterType::class;
    }

    protected function getRightFieldType(): string
    {
        return DateFilterType::class;
    }

    protected function getLeftFieldName(): string
    {
        return 'left_date';
    }

    protected function getRightFieldName(): string
    {
        return 'right_date';
    }

    protected function getLeftOptionsKey(): string
    {
        return 'left_date_options';
    }

    protected function getRightOptionsKey(): string
    {
        return 'right_date_options';
    }

    public function getBlockPrefix(): string
    {
        return 'filter_date_range';
    }
}
