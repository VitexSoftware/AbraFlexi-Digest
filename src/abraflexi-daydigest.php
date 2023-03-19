<?php

/**
 * AbraFlexi Digest - Everyday digest
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2018-2023 Vitex Software
 */

namespace AbraFlexi\Digest;

define('EASE_APPNAME', 'AbraFlexiDayDigest');

require_once __DIR__ . '/init.php';

$period = new \DateTime();

$subject = \sprintf(_('AbraFlexi %s Daily digest for %s'), $myCompanyName,
        \strftime('%x', $period->getTimestamp()));

$digestor = new Digestor($subject);
$digestor->dig($period, [\Ease\Functions::cfg('MODULE_DAILY_PATH'), \Ease\Functions::cfg('MODULE_PATH')]);
