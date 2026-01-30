<?php

/*
 * This file is part of the composer-write-changelogs project.
 *
 * (c) Dev Spiriit <dev@spiriit.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spiriit\Bundle\FormFilterBundle\Attribute;

use Attribute;

/**
 * Marks an entity or DTO class as having filterable properties.
 *
 * When placed on a class, the FilterReader will scan all properties
 * for #[Filter] attributes to automatically generate form filters.
 *
 * @author Spiriit <dev@spiriit.com>
 */
#[Attribute(Attribute::TARGET_CLASS)]
class Filterable
{
    public function __construct(
        /**
         * Optional: specify which properties to include.
         * If empty, all properties with #[Filter] attributes will be used.
         *
         * @var string[]
         */
        public array $include = [],

        /**
         * Optional: specify which properties to exclude.
         *
         * @var string[]
         */
        public array $exclude = [],

        /**
         * Optional: the root alias to use in queries.
         */
        public ?string $alias = null,
    ) {
    }
}
