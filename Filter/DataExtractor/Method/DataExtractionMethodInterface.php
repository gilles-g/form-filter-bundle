<?php

declare(strict_types=1);

/*
 * This file is part of the composer-write-changelogs project.
 *
 * (c) Dev Spiriit <dev@spiriit.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spiriit\Bundle\FormFilterBundle\Filter\DataExtractor\Method;

use Symfony\Component\Form\FormInterface;

/**
 * Defines methods for a data extraction method class.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 * @author Gilles Gauthier <g.gauthier@lexik.fr>
 */
interface DataExtractionMethodInterface
{
    /**
     * Returns the extraction method name.
     */
    public function getName(): string;

    /**
     * Extract data from a form.
     */
    public function extract(FormInterface $form): array;
}
