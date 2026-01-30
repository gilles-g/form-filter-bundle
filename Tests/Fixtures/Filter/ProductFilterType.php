<?php

/*
 * This file is part of the composer-write-changelogs project.
 *
 * (c) Dev Spiriit <dev@spiriit.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spiriit\Bundle\FormFilterBundle\Tests\Fixtures\Filter;

use Spiriit\Bundle\FormFilterBundle\Attribute\AbstractAutoFilterType;
use Spiriit\Bundle\FormFilterBundle\Tests\Fixtures\Entity\Product;

/**
 * Filter form type that uses attributes from the Product entity.
 *
 * @author Spiriit <dev@spiriit.com>
 */
class ProductFilterType extends AbstractAutoFilterType
{
    protected function getFilterableClass(): string
    {
        return Product::class;
    }
}
