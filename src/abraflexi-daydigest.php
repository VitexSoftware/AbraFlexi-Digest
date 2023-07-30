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
$period = new \DatePeriod(new \DateTime, new \DateInterval('P1D'), new \DateTime);
$fmt = datefmt_create(
        'cs_CZ',
        \IntlDateFormatter::FULL,
        \IntlDateFormatter::FULL,
        'Europe/Prague',
        \IntlDateFormatter::GREGORIAN
);
$subject = \sprintf(_('AbraFlexi Daily digest for  %s %s'), $myCompanyName, datefmt_format($fmt, (new \DateTime)->getTimestamp()));
$digestor = new Digestor($subject);
$digestor->dig($period, array_merge(\Ease\Functions::loadClassesInNamespace('AbraFlexi\Digest\Modules'), \Ease\Functions::loadClassesInNamespace('AbraFlexi\Digest\Modules\Daily')));
