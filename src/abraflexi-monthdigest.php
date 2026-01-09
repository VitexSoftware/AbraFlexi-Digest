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

\define('EASE_APPNAME', 'AbraFlexi 🌜 Mothly Digest');

require_once __DIR__.'/init.php';
$start = new \DateTime();
$start->modify('-1 month');
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

// Helper to validate formatter objects and avoid "unconstructed" fatals
$isFormatterValid = static function ($fmt): bool {
    if (!$fmt instanceof \IntlDateFormatter) {
        return false;
    }
    // When the formatter is not properly constructed, error code is not zero
    $code = \datefmt_get_error_code($fmt);
    return function_exists('intl_is_failure') ? !\intl_is_failure($code) : ($code === U_ZERO_ERROR);
};

// Format dates with error handling
if ($fmt !== false && $isFormatterValid($fmt)) {
    $startDateFormatted = \datefmt_format($fmt, $period->getStartDate()->getTimestamp());
    $endDateFormatted = \datefmt_format($fmt, $period->getEndDate()->getTimestamp());
    
    if ($startDateFormatted === false) {
        $startDateFormatted = $period->getStartDate()->format('Y-m-d');
    }
    
    if ($endDateFormatted === false) {
        $endDateFormatted = $period->getEndDate()->format('Y-m-d');
    }
} else {
    $startDateFormatted = $period->getStartDate()->format('Y-m-d');
    $endDateFormatted = $period->getEndDate()->format('Y-m-d');
}

$subject = sprintf(
    _('AbraFlexi %s 🌜 monthly digest from %s to %s'),
    $myCompanyName,
    $startDateFormatted,
    $endDateFormatted,
);
$digestor = new Digestor($subject);

$digestor->addItem(new \Ease\Html\DivTag(sprintf(
    _('from %s to %s'),
    $startDateFormatted,
    $endDateFormatted,
)));

$digestor->dig($period, array_merge(\Ease\Functions::loadClassesInNamespace('AbraFlexi\Digest\Modules'), \Ease\Functions::loadClassesInNamespace('AbraFlexi\Digest\Modules\Monthly')));
