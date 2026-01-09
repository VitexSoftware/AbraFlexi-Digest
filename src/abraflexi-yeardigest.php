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

// Create IntlDateFormatter with proper error handling (procedural API)
$locale = \Ease\Locale::$localeUsed ?? 'en_US';

try {
    $formatter = \datefmt_create($locale, \IntlDateFormatter::LONG, \IntlDateFormatter::NONE, 'Europe/Prague');
} catch (\ValueError $e) {
    $formatter = false;
}

// If creation failed, try with a fallback locale
if ($formatter === false) {
    try {
        $formatter = \datefmt_create('en_US', \IntlDateFormatter::LONG, \IntlDateFormatter::NONE, 'Europe/Prague');
    } catch (\ValueError $e) {
        $formatter = false;
    }
}

// Format dates with fallback if formatter failed
if ($formatter !== false) {
    $startFormatted = \datefmt_format($formatter, $period->getStartDate()->getTimestamp());
    $endFormatted = \datefmt_format($formatter, $period->getEndDate()->getTimestamp());

    if ($startFormatted === false) {
        $startFormatted = $period->getStartDate()->format('Y-m-d');
    }

    if ($endFormatted === false) {
        $endFormatted = $period->getEndDate()->format('Y-m-d');
    }
} else {
    $startFormatted = $period->getStartDate()->format('Y-m-d');
    $endFormatted = $period->getEndDate()->format('Y-m-d');
}

$digestor->addItem(new \Ease\Html\DivTag(sprintf(
    _('from %s to %s'),
    $startFormatted,
    $endFormatted,
)));

$digestor->dig($period, array_merge(\Ease\Functions::loadClassesInNamespace('AbraFlexi\Digest\Modules'), \Ease\Functions::loadClassesInNamespace('AbraFlexi\Digest\Modules\Yearly')));
