<?php

declare(strict_types=1);

/**
 * Html Table with Outlook tweaks
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2023 Vitex Software
 */

namespace AbraFlexi\Digest\Outlook;

/**
 * Description of TableTag
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
