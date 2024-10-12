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

namespace AbraFlexi\Digest\Outlook;

/**
 * Description of TableTag.
 *
 * @author vitex
 */
class TableTag extends \Ease\Html\TableTag
{
    /**
     * Html Table.
     *
     * @param mixed $content    inserted value
     * @param array $properties table tag properties
     */
    public function __construct($content = null, $properties = [])
    {
        $properties['role'] = 'presentation';
        $properties['cellspacing'] = '0';
        $properties['cellpadding'] = '0';
        $properties['border'] = '0';
        parent::__construct($content, $properties);
    }
}
