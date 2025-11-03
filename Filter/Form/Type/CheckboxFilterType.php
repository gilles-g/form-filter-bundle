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

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * Filter type for boolean.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class CheckboxFilterType extends AbstractSimpleFilterType
{
    public function getParent(): ?string
    {
        return CheckboxType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'filter_checkbox';
    }
}
