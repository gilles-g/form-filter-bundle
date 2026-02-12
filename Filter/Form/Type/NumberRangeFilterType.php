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

use Spiriit\Bundle\FormFilterBundle\Filter\FilterOperands;

/**
 * Filter type for numbers.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class NumberRangeFilterType extends AbstractRangeFilterType
{
    protected function getLeftFieldType(): string
    {
        return NumberFilterType::class;
    }

    protected function getRightFieldType(): string
    {
        return NumberFilterType::class;
    }

    protected function getLeftFieldName(): string
    {
        return 'left_number';
    }

    protected function getRightFieldName(): string
    {
        return 'right_number';
    }

    protected function getLeftOptionsKey(): string
    {
        return 'left_number_options';
    }

    protected function getRightOptionsKey(): string
    {
        return 'right_number_options';
    }

    protected function getDefaultLeftFieldOptions(): array
    {
        return ['condition_operator' => FilterOperands::OPERATOR_GREATER_THAN_EQUAL];
    }

    protected function getDefaultRightFieldOptions(): array
    {
        return ['condition_operator' => FilterOperands::OPERATOR_LOWER_THAN_EQUAL];
    }

    public function getBlockPrefix(): string
    {
        return 'filter_number_range';
    }
}
