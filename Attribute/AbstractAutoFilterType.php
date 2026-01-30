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

use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\SharedableFilterType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Abstract form type that automatically builds filters from class attributes.
 *
 * Extend this class and set the entity/DTO class to automatically
 * generate filter form fields from #[Filter] attributes.
 *
 * Example usage:
 * ```php
 * class ItemFilterType extends AbstractAutoFilterType
 * {
 *     protected function getFilterableClass(): string
 *     {
 *         return Item::class;
 *     }
 * }
 * ```
 *
 * @author Spiriit <dev@spiriit.com>
 */
abstract class AbstractAutoFilterType extends AbstractType
{
    protected AutoFilterTypeFactory $factory;

    public function __construct(?AutoFilterTypeFactory $factory = null)
    {
        // Allow null for backwards compatibility, will use default reader if not provided
        $this->factory = $factory ?? new AutoFilterTypeFactory(new FilterableReader());
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $class = $options['filterable_class'] ?? $this->getFilterableClass();

        if ($class !== null) {
            $this->factory->buildFilters($builder, $class);
        }

        // Allow child classes to add additional fields
        $this->buildAdditionalFields($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'filterable_class' => null,
        ]);

        $resolver->setAllowedTypes('filterable_class', ['null', 'string']);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return SharedableFilterType::class;
    }

    /**
     * Returns the entity/DTO class to read attributes from.
     * Override this method to specify the class.
     *
     * @return class-string|null
     */
    protected function getFilterableClass(): ?string
    {
        return null;
    }

    /**
     * Override this method to add additional form fields
     * that are not defined via attributes.
     */
    protected function buildAdditionalFields(FormBuilderInterface $builder, array $options): void
    {
        // Override in child classes if needed
    }
}
