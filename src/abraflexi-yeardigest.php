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

try {
    $fmt = datefmt_create(
        'cs_CZ',
        \IntlDateFormatter::SHORT,
        \IntlDateFormatter::NONE,
        'Europe/Prague',
        \IntlDateFormatter::GREGORIAN,
    );
} catch (\ValueError $e) {
    $fmt = false;
}

// Check if datefmt_create failed and create fallback
if ($fmt === false) {
    try {
        $fmt = datefmt_create(
            'en_US',
            \IntlDateFormatter::SHORT,
            \IntlDateFormatter::NONE,
            'UTC',
            \IntlDateFormatter::GREGORIAN,
        );
    } catch (\ValueError $e) {
        // If even the fallback fails, we'll handle it later
        $fmt = false;
    }
}

$myCompany = new \AbraFlexi\Company(\Ease\Shared::cfg('ABRAFLEXI_COMPANY'));
$myCompanyName = $myCompany->getDataValue('nazev');

$subject = sprintf(_('AbraFlexi %s 🎆 Year digest'), $myCompanyName);
$digestor = new Digestor($subject);

// Create IntlDateFormatter with fallback locale
$locale = \Ease\Locale::$localeUsed ?? 'en_US';
$formatter = \datefmt_create($locale, \IntlDateFormatter::LONG, \IntlDateFormatter::NONE, 'Europe/Prague');

// If the constructor failed, try with a fallback locale
if (!$formatter) {
    $formatter = \datefmt_create('en_US', \IntlDateFormatter::LONG, \IntlDateFormatter::NONE, 'Europe/Prague');
}

// Check if formatter is still false (should not happen with en_US)
if (!$formatter) {
    throw new \Exception('Failed to create IntlDateFormatter');
}

$digestor->addItem(new \Ease\Html\DivTag(sprintf(
    _('from %s to %s'),
    $formatter->format($period->getStartDate()->getTimestamp()),
    $formatter->format($period->getEndDate()->getTimestamp()),
)));

$digestor->dig($period, array_merge(\Ease\Functions::loadClassesInNamespace('AbraFlexi\Digest\Modules'), \Ease\Functions::loadClassesInNamespace('AbraFlexi\Digest\Modules\Yearly')));
