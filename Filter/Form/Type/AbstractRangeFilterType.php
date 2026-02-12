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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Abstract base class for range filter types that share common configuration.
 */
abstract class AbstractRangeFilterType extends AbstractType
{
    /**
     * Get the field type for the left bound.
     *
     * @return string
     */
    abstract protected function getLeftFieldType(): string;

    /**
     * Get the field type for the right bound.
     *
     * @return string
     */
    abstract protected function getRightFieldType(): string;

    /**
     * Get the name for the left field.
     *
     * @return string
     */
    abstract protected function getLeftFieldName(): string;

    /**
     * Get the name for the right field.
     *
     * @return string
     */
    abstract protected function getRightFieldName(): string;

    /**
     * Get the name for the left options key.
     *
     * @return string
     */
    abstract protected function getLeftOptionsKey(): string;

    /**
     * Get the name for the right options key.
     *
     * @return string
     */
    abstract protected function getRightOptionsKey(): string;

    /**
     * Get default options for left field.
     *
     * @return array
     */
    protected function getDefaultLeftFieldOptions(): array
    {
        return [];
    }

    /**
     * Get default options for right field.
     *
     * @return array
     */
    protected function getDefaultRightFieldOptions(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $leftFieldName = $this->getLeftFieldName();
        $rightFieldName = $this->getRightFieldName();
        $leftOptionsKey = $this->getLeftOptionsKey();
        $rightOptionsKey = $this->getRightOptionsKey();

        $builder->add($leftFieldName, $this->getLeftFieldType(), $options[$leftOptionsKey]);
        $builder->add($rightFieldName, $this->getRightFieldType(), $options[$rightOptionsKey]);

        $builder->setAttribute('filter_value_keys', [
            $leftFieldName => $options[$leftOptionsKey],
            $rightFieldName => $options[$rightOptionsKey]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $leftOptionsKey = $this->getLeftOptionsKey();
        $rightOptionsKey = $this->getRightOptionsKey();

        $resolver
            ->setDefaults([
                'required' => false,
                $leftOptionsKey => $this->getDefaultLeftFieldOptions(),
                $rightOptionsKey => $this->getDefaultRightFieldOptions(),
                'data_extraction_method' => 'value_keys'
            ])
            ->setAllowedValues('data_extraction_method', ['value_keys'])
        ;
    }
}
