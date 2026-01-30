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

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Spiriit\Bundle\FormFilterBundle\Filter\FilterOperands;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\BooleanFilterType;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\DateFilterType;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\DateTimeFilterType;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\NumberFilterType;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\TextFilterType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Factory that automatically builds form filters from entity/DTO attributes.
 *
 * This service reads #[Filter] attributes from a class and creates
 * the corresponding form fields, reducing boilerplate code.
 *
 * @author Spiriit <dev@spiriit.com>
 */
class AutoFilterTypeFactory
{
    /**
     * Maps PHP types to filter form types.
     *
     * @var array<string, class-string>
     */
    private const TYPE_MAPPING = [
        'string' => TextFilterType::class,
        'int' => NumberFilterType::class,
        'integer' => NumberFilterType::class,
        'float' => NumberFilterType::class,
        'double' => NumberFilterType::class,
        'bool' => BooleanFilterType::class,
        'boolean' => BooleanFilterType::class,
        DateTime::class => DateTimeFilterType::class,
        DateTimeImmutable::class => DateTimeFilterType::class,
        DateTimeInterface::class => DateTimeFilterType::class,
    ];

    public function __construct(
        private FilterableReader $reader,
    ) {
    }

    /**
     * Builds filter form fields from a class's attributes.
     *
     * @param class-string|object $class The entity/DTO class to read attributes from
     */
    public function buildFilters(FormBuilderInterface $builder, string|object $class): void
    {
        $filters = $this->reader->getFilters($class);

        foreach ($filters as $propertyName => $filter) {
            $type = $this->resolveFilterType($filter, $class, $propertyName);
            $options = $this->buildOptions($filter, $class, $propertyName);

            $fieldName = $filter->name ?? $propertyName;

            $builder->add($fieldName, $type, $options);
        }
    }

    /**
     * Gets filter form field configuration for manual building.
     *
     * @param class-string|object $class
     * @return array<string, array{type: class-string, options: array<string, mixed>}>
     */
    public function getFilterConfiguration(string|object $class): array
    {
        $filters = $this->reader->getFilters($class);
        $config = [];

        foreach ($filters as $propertyName => $filter) {
            $type = $this->resolveFilterType($filter, $class, $propertyName);
            $options = $this->buildOptions($filter, $class, $propertyName);

            $fieldName = $filter->name ?? $propertyName;

            $config[$fieldName] = [
                'type' => $type,
                'options' => $options,
            ];
        }

        return $config;
    }

    /**
     * Resolves the filter form type to use.
     *
     * @param class-string|object $class
     * @return class-string
     */
    private function resolveFilterType(Filter $filter, string|object $class, string $propertyName): string
    {
        // If type is explicitly set, use it
        if ($filter->type !== null) {
            return $filter->type;
        }

        // Auto-detect from property type
        $phpType = $this->reader->getPropertyType($class, $propertyName);

        if ($phpType !== null && isset(self::TYPE_MAPPING[$phpType])) {
            return self::TYPE_MAPPING[$phpType];
        }

        // Default to text filter
        return TextFilterType::class;
    }

    /**
     * Builds form field options from the Filter attribute.
     *
     * @param class-string|object $class
     * @return array<string, mixed>
     */
    private function buildOptions(Filter $filter, string|object $class, string $propertyName): array
    {
        $options = $filter->options;

        // Set required
        $options['required'] = $filter->required;

        // Set label if specified
        if ($filter->label !== null) {
            $options['label'] = $filter->label;
        }

        // Set field name for query if specified
        if ($filter->fieldName !== null) {
            $options['filter_field_name'] = $filter->fieldName;
        }

        // Set condition operator for number filters
        if ($filter->operator !== null) {
            $options['condition_operator'] = $filter->operator;
        }

        // Set condition pattern for text filters
        if ($filter->pattern !== null) {
            $options['condition_pattern'] = $filter->pattern;
        }

        return $options;
    }
}
