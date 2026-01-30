<?php

/*
 * This file is part of the composer-write-changelogs project.
 *
 * (c) Dev Spiriit <dev@spiriit.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spiriit\Bundle\FormFilterBundle\Tests\Attribute;

use Spiriit\Bundle\FormFilterBundle\Attribute\AbstractAutoFilterType;
use Spiriit\Bundle\FormFilterBundle\Attribute\AutoFilterTypeFactory;
use Spiriit\Bundle\FormFilterBundle\Attribute\FilterableReader;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\FilterExtension;
use Spiriit\Bundle\FormFilterBundle\Tests\Fixtures\Entity\Product;
use Spiriit\Bundle\FormFilterBundle\Tests\Fixtures\Filter\ProductFilterType;
use Symfony\Component\Form\Extension\Core\CoreExtension;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormRegistry;
use Symfony\Component\Form\ResolvedFormTypeFactory;

/**
 * Integration tests for the AbstractAutoFilterType.
 *
 * @author Spiriit <dev@spiriit.com>
 */
class AbstractAutoFilterTypeTest extends \PHPUnit\Framework\TestCase
{
    private FormFactory $formFactory;

    protected function setUp(): void
    {
        $resolvedFormTypeFactory = new ResolvedFormTypeFactory();
        $registry = new FormRegistry(
            [new CoreExtension(), new FilterExtension()],
            $resolvedFormTypeFactory
        );
        $this->formFactory = new FormFactory($registry, $resolvedFormTypeFactory);
    }

    public function testProductFilterTypeCreatesAllFields(): void
    {
        $form = $this->formFactory->create(ProductFilterType::class);

        // Verify all filter fields are created
        $this->assertTrue($form->has('name'));
        $this->assertTrue($form->has('price'));
        $this->assertTrue($form->has('stock'));
        $this->assertTrue($form->has('active'));
        $this->assertTrue($form->has('createdAt'));

        // Description has no #[Filter] attribute, shouldn't be present
        $this->assertFalse($form->has('description'));
    }

    public function testProductFilterTypeSubmission(): void
    {
        $form = $this->formFactory->create(ProductFilterType::class);

        $form->submit([
            'name' => 'Test Product',
            'price' => 100.0,
            'stock' => 10,
            'active' => 'y',
        ]);

        $this->assertTrue($form->isSynchronized());

        $data = $form->getData();
        $this->assertIsArray($data);
        $this->assertEquals('Test Product', $data['name']);
    }

    public function testFilterableClassOptionOverridesDefault(): void
    {
        // Create an anonymous filter type without a default class
        $filterType = new class extends AbstractAutoFilterType {
            protected function getFilterableClass(): ?string
            {
                return null; // No default
            }
        };

        // Create form with filterable_class option
        $form = $this->formFactory->create(get_class($filterType), null, [
            'filterable_class' => Product::class,
        ]);

        // Should have Product's filter fields
        $this->assertTrue($form->has('name'));
        $this->assertTrue($form->has('price'));
    }

    public function testFormWithNoFilterableClassHasNoFields(): void
    {
        $filterType = new class extends AbstractAutoFilterType {
            protected function getFilterableClass(): ?string
            {
                return null;
            }
        };

        $form = $this->formFactory->create(get_class($filterType));

        // No fields should be added
        $this->assertCount(0, $form);
    }
}
