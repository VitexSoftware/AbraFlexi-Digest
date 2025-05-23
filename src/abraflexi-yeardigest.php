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

\define('EASE_APPNAME', 'AbraFlexi 🎆 Digest');

require_once __DIR__.'/init.php';

$start = new \DateTime();
$start->modify('-1 year');
$end = new \DateTime();
$period = new \DatePeriod($start, new \DateInterval('P1D'), $end);
$fmt = datefmt_create(
    'cs_CZ',
    \IntlDateFormatter::SHORT,
    \IntlDateFormatter::NONE,
    'Europe/Prague',
    \IntlDateFormatter::GREGORIAN,
);

$myCompany = new \AbraFlexi\Company(\Ease\Shared::cfg('ABRAFLEXI_COMPANY'));
$myCompanyName = $myCompany->getDataValue('nazev');

$subject = sprintf(_('AbraFlexi %s 🎆 Year digest'), $myCompanyName);
$digestor = new Digestor($subject);
$formatter = new \IntlDateFormatter(\Ease\Locale::$localeUsed, \IntlDateFormatter::LONG, \IntlDateFormatter::NONE);
$digestor->addItem(new \Ease\Html\DivTag(sprintf(
    _('from %s to %s'),
    $formatter->format($period->getStartDate()->getTimestamp()),
    $formatter->format($period->getEndDate()->getTimestamp()),
)));

$digestor->dig($period, array_merge(\Ease\Functions::loadClassesInNamespace('AbraFlexi\Digest\Modules'), \Ease\Functions::loadClassesInNamespace('AbraFlexi\Digest\Modules\Yearly')));
