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
        \IntlDateFormatter::SHORT,
        \IntlDateFormatter::NONE,
        'Europe/Prague',
        \IntlDateFormatter::GREGORIAN
);
$formatter = new \IntlDateFormatter(\Ease\Locale::$localeUsed, \IntlDateFormatter::LONG, \IntlDateFormatter::NONE);
$period = new \DatePeriod($start, new \DateInterval('P1D'), $end);
$subject = sprintf(
        _('AbraFlexi %s Weekly digest from %s to %s'), $myCompanyName,
        $formatter->format($period->getStartDate()->getTimestamp()),
        $formatter->format($period->getEndDate()->getTimestamp())
);
$digestor = new Digestor($subject);
$digestor->dig($period, array_merge(\Ease\Functions::loadClassesInNamespace('AbraFlexi\Digest\Modules'), \Ease\Functions::loadClassesInNamespace('AbraFlexi\Digest\Modules\Weekly')));
