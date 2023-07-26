<?php

/**
 * AbraFlexi Digest - Monthly 
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2018-2023 Vitex Software
 */

namespace AbraFlexi\Digest;

define('EASE_APPNAME', 'AbraFlexiMonthDigest');
require_once __DIR__ . '/init.php';
$start = new \DateTime();
$start->modify('-1 month');
$end = new \DateTime();
$period = new \DatePeriod($start, new \DateInterval('P1D'), $end);
$fmt = datefmt_create(
        'cs_CZ',
        \IntlDateFormatter::FULL,
        \IntlDateFormatter::FULL,
        'Europe/Prague',
        \IntlDateFormatter::GREGORIAN
);
$subject = sprintf(
        _('AbraFlexi %s Monthly digest from %s to %s'), $myCompanyName,
        \datefmt_format($fmt, $period->getStartDate()->getTimestamp()),
        \datefmt_format($fmt, $period->getEndDate()->getTimestamp())
);
$digestor = new Digestor($subject);
$digestor->dig($period,
        [constant('MODULE_MONTHLY_PATH'), constant('MODULE_PATH')]);