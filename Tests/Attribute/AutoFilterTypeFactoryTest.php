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

use PHPUnit\Framework\TestCase;
use Spiriit\Bundle\FormFilterBundle\Attribute\AutoFilterTypeFactory;
use Spiriit\Bundle\FormFilterBundle\Attribute\FilterableReader;
use Spiriit\Bundle\FormFilterBundle\Filter\FilterOperands;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\BooleanFilterType;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\DateFilterType;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\DateTimeFilterType;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\NumberFilterType;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\TextFilterType;
use Spiriit\Bundle\FormFilterBundle\Tests\Fixtures\Entity\Product;
use Spiriit\Bundle\FormFilterBundle\Tests\Fixtures\Entity\SearchCriteriaDto;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Tests for the AutoFilterTypeFactory service.
 *
 * @author Spiriit <dev@spiriit.com>
 */
class AutoFilterTypeFactoryTest extends TestCase
{
    private AutoFilterTypeFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new AutoFilterTypeFactory(new FilterableReader());
    }

    public function testGetFilterConfigurationFromProduct(): void
    {
        $config = $this->factory->getFilterConfiguration(Product::class);

        $this->assertCount(5, $config);
        $this->assertArrayHasKey('name', $config);
        $this->assertArrayHasKey('price', $config);
        $this->assertArrayHasKey('stock', $config);
        $this->assertArrayHasKey('active', $config);
        $this->assertArrayHasKey('createdAt', $config);
    }

    public function testGetFilterConfigurationReturnsCorrectTypes(): void
    {
        $config = $this->factory->getFilterConfiguration(Product::class);

        $this->assertEquals(TextFilterType::class, $config['name']['type']);
        $this->assertEquals(NumberFilterType::class, $config['price']['type']);
        $this->assertEquals(NumberFilterType::class, $config['stock']['type']);
        $this->assertEquals(BooleanFilterType::class, $config['active']['type']);
        $this->assertEquals(DateFilterType::class, $config['createdAt']['type']);
    }

    public function testGetFilterConfigurationReturnsCorrectOptions(): void
    {
        $config = $this->factory->getFilterConfiguration(Product::class);

        // Name filter options
        $this->assertEquals('Product Name', $config['name']['options']['label']);
        $this->assertEquals(FilterOperands::STRING_CONTAINS, $config['name']['options']['condition_pattern']);
        $this->assertFalse($config['name']['options']['required']);

        // Price filter options
        $this->assertEquals('Minimum Price', $config['price']['options']['label']);
        $this->assertEquals(FilterOperands::OPERATOR_GREATER_THAN_EQUAL, $config['price']['options']['condition_operator']);

        // Stock filter options
        $this->assertEquals(FilterOperands::OPERATOR_GREATER_THAN, $config['stock']['options']['condition_operator']);
    }

    public function testAutoTypeDetectionFromPropertyType(): void
    {
        $config = $this->factory->getFilterConfiguration(SearchCriteriaDto::class);

        // Auto-detected from property types
        $this->assertEquals(TextFilterType::class, $config['keyword']['type']);
        $this->assertEquals(NumberFilterType::class, $config['amount']['type']);
        $this->assertEquals(NumberFilterType::class, $config['quantity']['type']);
        $this->assertEquals(BooleanFilterType::class, $config['isEnabled']['type']);
        $this->assertEquals(DateTimeFilterType::class, $config['startDate']['type']);
    }

    public function testExcludedFieldsAreNotIncluded(): void
    {
        $config = $this->factory->getFilterConfiguration(SearchCriteriaDto::class);

        // internalField is excluded
        $this->assertArrayNotHasKey('internalField', $config);

        // regularField has no #[Filter] attribute
        $this->assertArrayNotHasKey('regularField', $config);
    }

    public function testBuildFiltersAddsFieldsToBuilder(): void
    {
        // Create a mock FormBuilder
        $builder = $this->createMock(FormBuilder::class);

        // Expect add() to be called 5 times for Product (name, price, stock, active, createdAt)
        $builder->expects($this->exactly(5))
            ->method('add')
            ->willReturnSelf();

        $this->factory->buildFilters($builder, Product::class);
    }

    public function testBuildFiltersWithCorrectFieldNames(): void
    {
        $builder = $this->createMock(FormBuilder::class);
        $addedFields = [];

        $builder->expects($this->exactly(5))
            ->method('add')
            ->willReturnCallback(function ($name, $type, $options) use (&$addedFields, $builder) {
                $addedFields[$name] = ['type' => $type, 'options' => $options];
                return $builder;
            });

        $this->factory->buildFilters($builder, Product::class);

        $this->assertArrayHasKey('name', $addedFields);
        $this->assertArrayHasKey('price', $addedFields);
        $this->assertArrayHasKey('stock', $addedFields);
        $this->assertArrayHasKey('active', $addedFields);
        $this->assertArrayHasKey('createdAt', $addedFields);
    }
}
