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
use Spiriit\Bundle\FormFilterBundle\Attribute\Filter;
use Spiriit\Bundle\FormFilterBundle\Attribute\Filterable;
use Spiriit\Bundle\FormFilterBundle\Attribute\FilterableReader;
use Spiriit\Bundle\FormFilterBundle\Filter\FilterOperands;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\BooleanFilterType;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\DateFilterType;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\NumberFilterType;
use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\TextFilterType;
use Spiriit\Bundle\FormFilterBundle\Tests\Fixtures\Entity\Product;
use Spiriit\Bundle\FormFilterBundle\Tests\Fixtures\Entity\SearchCriteriaDto;

/**
 * Tests for the FilterableReader service.
 *
 * @author Spiriit <dev@spiriit.com>
 */
class FilterableReaderTest extends TestCase
{
    private FilterableReader $reader;

    protected function setUp(): void
    {
        $this->reader = new FilterableReader();
    }

    public function testIsFilterableWithFilterableAttribute(): void
    {
        $this->assertTrue($this->reader->isFilterable(Product::class));
    }

    public function testIsFilterableWithoutFilterableAttribute(): void
    {
        // stdClass has no #[Filterable] attribute
        $this->assertFalse($this->reader->isFilterable(\stdClass::class));
    }

    public function testGetFilterableAttribute(): void
    {
        $filterable = $this->reader->getFilterableAttribute(Product::class);

        $this->assertInstanceOf(Filterable::class, $filterable);
        $this->assertEquals('p', $filterable->alias);
    }

    public function testGetFilterableAttributeReturnsNullWhenMissing(): void
    {
        $filterable = $this->reader->getFilterableAttribute(\stdClass::class);

        $this->assertNull($filterable);
    }

    public function testGetFiltersFromProduct(): void
    {
        $filters = $this->reader->getFilters(Product::class);

        // Product should have 5 filterable properties (name, price, stock, active, createdAt)
        $this->assertCount(5, $filters);
        $this->assertArrayHasKey('name', $filters);
        $this->assertArrayHasKey('price', $filters);
        $this->assertArrayHasKey('stock', $filters);
        $this->assertArrayHasKey('active', $filters);
        $this->assertArrayHasKey('createdAt', $filters);
        $this->assertArrayNotHasKey('description', $filters); // No #[Filter] attribute
    }

    public function testGetFiltersReadsFilterAttributes(): void
    {
        $filters = $this->reader->getFilters(Product::class);

        // Check name filter configuration
        $nameFilter = $filters['name'];
        $this->assertInstanceOf(Filter::class, $nameFilter);
        $this->assertEquals(TextFilterType::class, $nameFilter->type);
        $this->assertEquals('Product Name', $nameFilter->label);
        $this->assertEquals(FilterOperands::STRING_CONTAINS, $nameFilter->pattern);

        // Check price filter configuration
        $priceFilter = $filters['price'];
        $this->assertEquals(NumberFilterType::class, $priceFilter->type);
        $this->assertEquals(FilterOperands::OPERATOR_GREATER_THAN_EQUAL, $priceFilter->operator);
    }

    public function testGetFiltersRespectsExcludeList(): void
    {
        $filters = $this->reader->getFilters(SearchCriteriaDto::class);

        // internalField is excluded via #[Filterable(exclude: ['internalField'])]
        $this->assertArrayNotHasKey('internalField', $filters);

        // Other properties with #[Filter] should be present
        $this->assertArrayHasKey('keyword', $filters);
        $this->assertArrayHasKey('amount', $filters);
    }

    public function testGetFilterForSpecificProperty(): void
    {
        $filter = $this->reader->getFilter(Product::class, 'active');

        $this->assertInstanceOf(Filter::class, $filter);
        $this->assertEquals(BooleanFilterType::class, $filter->type);
    }

    public function testGetFilterReturnsNullForPropertyWithoutAttribute(): void
    {
        $filter = $this->reader->getFilter(Product::class, 'description');

        $this->assertNull($filter);
    }

    public function testGetFilterReturnsNullForNonExistentProperty(): void
    {
        $filter = $this->reader->getFilter(Product::class, 'nonExistent');

        $this->assertNull($filter);
    }

    public function testGetPropertyType(): void
    {
        $this->assertEquals('string', $this->reader->getPropertyType(Product::class, 'name'));
        $this->assertEquals('float', $this->reader->getPropertyType(Product::class, 'price'));
        $this->assertEquals('int', $this->reader->getPropertyType(Product::class, 'stock'));
        $this->assertEquals('bool', $this->reader->getPropertyType(Product::class, 'active'));
    }

    public function testGetPropertyTypeWithNullableProperty(): void
    {
        // createdAt is ?DateTime
        $type = $this->reader->getPropertyType(Product::class, 'createdAt');
        $this->assertEquals('DateTime', $type);
    }

    public function testGetPropertyTypeReturnsNullForNonExistentProperty(): void
    {
        $type = $this->reader->getPropertyType(Product::class, 'nonExistent');
        $this->assertNull($type);
    }

    public function testFilterNameDefaultsToPropertyName(): void
    {
        $filter = $this->reader->getFilter(Product::class, 'active');

        // Name should be set to property name when not explicitly specified
        $this->assertEquals('active', $filter->name);
    }
}
