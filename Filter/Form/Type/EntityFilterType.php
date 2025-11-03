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

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Filter type for related entities.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class EntityFilterType extends AbstractSimpleFilterType
{
    public function getParent(): ?string
    {
        return EntityType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'filter_entity';
    }
}
