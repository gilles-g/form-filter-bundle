<?php

/*
 * This file is part of the composer-write-changelogs project.
 *
 * (c) Dev Spiriit <dev@spiriit.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spiriit\Bundle\FormFilterBundle\Tests\Fixtures\Entity;

use DateTimeImmutable;
use Spiriit\Bundle\FormFilterBundle\Attribute\Filter;
use Spiriit\Bundle\FormFilterBundle\Attribute\Filterable;

/**
 * DTO with #[Filter] attributes demonstrating auto-detection of filter types.
 *
 * @author Spiriit <dev@spiriit.com>
 */
#[Filterable(exclude: ['internalField'])]
class SearchCriteriaDto
{
    #[Filter]
    public string $keyword = '';

    #[Filter(label: 'Min Amount')]
    public float $amount = 0.0;

    #[Filter]
    public int $quantity = 0;

    #[Filter]
    public bool $isEnabled = false;

    #[Filter]
    public ?DateTimeImmutable $startDate = null;

    // Excluded by #[Filterable(exclude: ...)]
    #[Filter]
    public string $internalField = '';

    // No #[Filter] attribute, won't be included
    public string $regularField = '';
}
