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
 * Link to company.
 *
 * @author vitex
 *
 * @no-named-arguments
 */
class CompanyLink extends \Ease\Html\ATag
{
    /**
     * Link to company.
     *
     * @param string        $code
     * @param \AbraFlexi\RO $engine
     * @param array<string> $properties
     */
    public function __construct($code, $engine, $properties = [])
    {
        $engine->loadFromAbraFlexi($code);
        parent::__construct($engine->getApiURL(), $engine->getDataValue('nazev'), $properties);
    }
}
