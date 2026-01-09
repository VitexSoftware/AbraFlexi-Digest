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

\define('EASE_APPNAME', 'AbraFlexi 📆 WeekDigest');

require_once __DIR__.'/init.php';
$start = new \DateTime();
$start->modify('-1 week');
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

$period = new \DatePeriod($start, new \DateInterval('P1D'), $end);

$myCompany = new \AbraFlexi\Company(\Ease\Shared::cfg('ABRAFLEXI_COMPANY'));
$myCompanyName = $myCompany->getDataValue('nazev');

$subject = sprintf(_('AbraFlexi %s 📆 Weekly digest'), $myCompanyName);
$digestor = new Digestor($subject);

// Helper to validate formatter objects and avoid "unconstructed" fatals
$isFormatterValid = static function ($fmt): bool {
    if (!$fmt instanceof \IntlDateFormatter) {
        return false;
    }
    // When the formatter is not properly constructed, error code is not zero
    $code = \datefmt_get_error_code($fmt);
    return function_exists('intl_is_failure') ? !\intl_is_failure($code) : ($code === U_ZERO_ERROR);
};

// Format dates with fallback if formatter failed
if ($formatter !== false && $isFormatterValid($formatter)) {
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

$digestor->dig($period, array_merge(\Ease\Functions::loadClassesInNamespace('AbraFlexi\Digest\Modules'), \Ease\Functions::loadClassesInNamespace('AbraFlexi\Digest\Modules\Weekly')));
