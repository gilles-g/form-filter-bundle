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
use Spiriit\Bundle\FormFilterBundle\Filter\FilterOperands;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\TextFilterType;

/**
 * Configures a filter for an entity or DTO property.
 *
 * This attribute allows you to define filter configuration directly
 * on entity/DTO properties, reducing FormType boilerplate.
 *
 * @author Spiriit <dev@spiriit.com>
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Filter
{
    public function __construct(
        /**
         * The filter form type class to use (e.g., TextFilterType::class).
         * If not specified, it will be auto-detected based on the property type.
         *
         * @var class-string|null
         */
        public ?string $type = null,

        /**
         * The field name to use in the form and query.
         * If not specified, the property name will be used.
         */
        public ?string $name = null,

        /**
         * The label for the form field.
         * If not specified, Symfony will auto-generate it from the field name.
         */
        public ?string $label = null,

        /**
         * Additional options to pass to the form type.
         *
         * @var array<string, mixed>
         */
        public array $options = [],

        /**
         * The condition operator for numeric comparisons.
         * Use FilterOperands constants (e.g., FilterOperands::OPERATOR_EQUAL).
         */
        public ?string $operator = null,

        /**
         * The condition pattern for string comparisons.
         * Use FilterOperands constants (e.g., FilterOperands::STRING_CONTAINS).
         */
        public ?int $pattern = null,

        /**
         * Whether this filter field is required.
         */
        public bool $required = false,

        /**
         * Custom field name for the Doctrine query.
         * Useful when the entity property name differs from the DB column.
         */
        public ?string $fieldName = null,
    ) {
    }
}
