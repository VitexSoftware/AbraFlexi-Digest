<?php

/**
 * AbraFlexi Digest - Monthly 
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2018-21 Vitex Software
 */

namespace AbraFlexi\Digest;

define('EASE_APPNAME', 'AbraFlexiMonthDigest');

require_once __DIR__ . '/init.php';

$start = new \DateTime();
$start->modify('-1 month');
$end = new \DateTime();
$period = new \DatePeriod($start, new \DateInterval('P1D'), $end);

$subject = sprintf(
        _('AbraFlexi %s Monthly digest from %s to %s'), $myCompanyName,
        \strftime('%x', $period->getStartDate()->getTimestamp()),
        \strftime('%x', $period->getEndDate()->getTimestamp())
);

$digestor = new Digestor($subject);
$digestor->dig($period,
        [constant('MODULE_MONTHLY_PATH'), constant('MODULE_PATH')]);

