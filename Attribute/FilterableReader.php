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

use ReflectionClass;
use ReflectionProperty;

/**
 * Reads filter-related attributes from entity/DTO classes.
 *
 * This service uses PHP's Reflection API to extract #[Filterable] and #[Filter]
 * attributes from class definitions, enabling automatic filter form generation.
 *
 * @author Spiriit <dev@spiriit.com>
 */
class FilterableReader
{
    /**
     * Checks if a class has the #[Filterable] attribute.
     *
     * @param class-string|object $class
     */
    public function isFilterable(string|object $class): bool
    {
        $reflectionClass = new ReflectionClass($class);
        $attributes = $reflectionClass->getAttributes(Filterable::class);

        return count($attributes) > 0;
    }

    /**
     * Gets the #[Filterable] attribute configuration from a class.
     *
     * @param class-string|object $class
     */
    public function getFilterableAttribute(string|object $class): ?Filterable
    {
        $reflectionClass = new ReflectionClass($class);
        $attributes = $reflectionClass->getAttributes(Filterable::class);

        if (count($attributes) === 0) {
            return null;
        }

        return $attributes[0]->newInstance();
    }

    /**
     * Gets all #[Filter] attributes from a class's properties.
     *
     * @param class-string|object $class
     * @return array<string, Filter> Array keyed by property name
     */
    public function getFilters(string|object $class): array
    {
        $reflectionClass = new ReflectionClass($class);
        $filterable = $this->getFilterableAttribute($class);

        $filters = [];

        foreach ($reflectionClass->getProperties() as $property) {
            $propertyName = $property->getName();

            // Check include/exclude lists from #[Filterable]
            if ($filterable !== null) {
                if (!empty($filterable->include) && !in_array($propertyName, $filterable->include, true)) {
                    continue;
                }
                if (!empty($filterable->exclude) && in_array($propertyName, $filterable->exclude, true)) {
                    continue;
                }
            }

            $filterAttributes = $property->getAttributes(Filter::class);

            if (count($filterAttributes) > 0) {
                $filter = $filterAttributes[0]->newInstance();

                // Set default name if not specified
                if ($filter->name === null) {
                    $filter = new Filter(
                        type: $filter->type,
                        name: $propertyName,
                        label: $filter->label,
                        options: $filter->options,
                        operator: $filter->operator,
                        pattern: $filter->pattern,
                        required: $filter->required,
                        fieldName: $filter->fieldName,
                    );
                }

                $filters[$propertyName] = $filter;
            }
        }

        return $filters;
    }

    /**
     * Gets filter configuration for a specific property.
     *
     * @param class-string|object $class
     */
    public function getFilter(string|object $class, string $propertyName): ?Filter
    {
        $reflectionClass = new ReflectionClass($class);

        try {
            $property = $reflectionClass->getProperty($propertyName);
        } catch (\ReflectionException) {
            return null;
        }

        $filterAttributes = $property->getAttributes(Filter::class);

        if (count($filterAttributes) === 0) {
            return null;
        }

        $filter = $filterAttributes[0]->newInstance();

        // Set default name if not specified
        if ($filter->name === null) {
            $filter = new Filter(
                type: $filter->type,
                name: $propertyName,
                label: $filter->label,
                options: $filter->options,
                operator: $filter->operator,
                pattern: $filter->pattern,
                required: $filter->required,
                fieldName: $filter->fieldName,
            );
        }

        return $filter;
    }

    /**
     * Gets the PHP type of a property for auto-detection of filter type.
     *
     * @param class-string|object $class
     */
    public function getPropertyType(string|object $class, string $propertyName): ?string
    {
        $reflectionClass = new ReflectionClass($class);

        try {
            $property = $reflectionClass->getProperty($propertyName);
        } catch (\ReflectionException) {
            return null;
        }

        $type = $property->getType();

        if ($type === null) {
            return null;
        }

        if ($type instanceof \ReflectionNamedType) {
            return $type->getName();
        }

        // For union types, return the first non-null type
        if ($type instanceof \ReflectionUnionType) {
            foreach ($type->getTypes() as $unionType) {
                if ($unionType instanceof \ReflectionNamedType && $unionType->getName() !== 'null') {
                    return $unionType->getName();
                }
            }
        }

        return null;
    }
}
