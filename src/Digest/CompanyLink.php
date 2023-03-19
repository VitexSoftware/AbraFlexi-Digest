<?php

/**
 * AbraFlexi Digest - CompanyLink class.
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2020-2023 Vitex Software
 */

namespace AbraFlexi\Digest;

/**
 * Link to company
 *
 * @author vitex
 */
class CompanyLink extends \Ease\Html\ATag {

    public function __construct($code, $engine, $properties = []) {
        $engine->loadFromAbraFlexi($code);
        parent::__construct($engine->getApiURL(), $engine->getDataValue('nazev'), $properties);
    }

}
