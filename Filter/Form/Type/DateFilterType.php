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

use Symfony\Component\Form\Extension\Core\Type\DateType;

/**
 * Filter type for date field.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class DateFilterType extends AbstractSimpleFilterType
{
    public function getParent(): ?string
    {
        return DateType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'filter_date';
    }
}
