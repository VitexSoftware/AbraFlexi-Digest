<?php

declare(strict_types=1);

/**
 * This file is part of the AbraFlexi-Digest package
 *
 * https://github.com/VitexSoftware/AbraFlexi-Digest/
 *
 * (c) Vítězslav Dvořák <http://vitexsoftware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AbraFlexi\Digest;

/**
 * Description of Table.
 *
 * @deprecated since version 1.0 - use Outlook\TableTag instead
 *
 * @author vitex
 *
 * @no-named-arguments
 */
class Table extends Outlook\TableTag
{
    /**
     * Digest Table.
     *
     * @param array $thCols     TH Columns
     * @param array $properties
     */
    public function __construct($thCols, $properties = [])
    {
        parent::__construct(null, $properties);
        $this->addRowHeaderColumns($thCols);
        $this->addTagClass('table');
    }
}
