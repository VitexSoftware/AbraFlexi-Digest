<?php

/**
 * AbraFlexi Digest - Weekly 
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2018 Vitex Software
 */

namespace AbraFlexi\Digest;

define('EASE_APPNAME', 'AbraFlexiWeekDigest');

require_once __DIR__ . '/init.php';

$start = new \DateTime();
$start->modify('-1 week');
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
        _('AbraFlexi %s Weekly digest from %s to %s'),
        $myCompanyName,
        \datefmt_format($fmt, $period->getStartDate()->getTimestamp()),
        \datefmt_format($fmt, $period->getEndDate()->getTimestamp())
);

$digestor = new Digestor($subject);
$digestor->dig(
        $period,
        [constant('MODULE_WEEKLY_PATH'), constant('MODULE_PATH')]
);