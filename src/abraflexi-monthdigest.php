<?php

/**
 * AbraFlexi Digest - Monthly
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2018-2023 Vitex Software
 */

namespace AbraFlexi\Digest;

define('EASE_APPNAME', 'AbraFlexi MonthDigest');
require_once __DIR__ . '/init.php';
$start = new \DateTime();
$start->modify('-1 month');
$end = new \DateTime();
$period = new \DatePeriod($start, new \DateInterval('P1D'), $end);
$fmt = datefmt_create(
    'cs_CZ',
    \IntlDateFormatter::SHORT,
    \IntlDateFormatter::NONE,
    'Europe/Prague',
    \IntlDateFormatter::GREGORIAN
);
$subject = sprintf(
    _('AbraFlexi %s Monthly digest from %s to %s'),
    $myCompanyName,
    \datefmt_format($fmt, $period->getStartDate()->getTimestamp()),
    \datefmt_format($fmt, $period->getEndDate()->getTimestamp())
);
$digestor = new Digestor($subject);
$formatter = new \IntlDateFormatter(\Ease\Locale::$localeUsed, \IntlDateFormatter::LONG, \IntlDateFormatter::NONE);
$digestor->addItem(new \Ease\Html\DivTag(sprintf(
    _('from %s to %s'),
    $formatter->format($period->getStartDate()->getTimestamp()),
    $formatter->format($period->getEndDate()->getTimestamp())
)));

$digestor->dig($period, array_merge(\Ease\Functions::loadClassesInNamespace('AbraFlexi\Digest\Modules'), \Ease\Functions::loadClassesInNamespace('AbraFlexi\Digest\Modules\Monthly')));
