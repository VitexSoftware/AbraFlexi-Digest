<?php

/**
 * AbraFlexi Digest - Dayly 
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2018 Vitex Software
 */

namespace AbraFlexi\Digest;

define('EASE_APPNAME', 'AbraFlexiDayDigest');

require_once __DIR__ . '/init.php';

$period = new \DateTime();

$subject = \sprintf(_('AbraFlexi %s Daily digest for %s'), $myCompanyName,
        \strftime('%x', $period->getTimestamp()));

$digestor = new Digestor($subject);
$digestor->dig($period, [constant('MODULE_DAILY_PATH'), constant('MODULE_PATH')]);
