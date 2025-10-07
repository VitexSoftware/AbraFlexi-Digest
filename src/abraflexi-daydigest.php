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

\define('EASE_APPNAME', 'AbraFlexi 🌅 Daily Digest');

require_once __DIR__.'/init.php';
$period = new \DatePeriod(new \DateTime(), new \DateInterval('P1D'), new \DateTime());

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

$subject = \sprintf(_('AbraFlexi 🌅 Daily digest for  %s'), $myCompanyName);
$digestor = new Digestor($subject);

// Format date with error handling
$formattedDate = datefmt_format($fmt, (new \DateTime())->getTimestamp());

if ($formattedDate === false) {
    $formattedDate = (new \DateTime())->format('Y-m-d'); // Fallback format
}

$digestor->addItem(new \Ease\Html\DivTag($formattedDate));
$digestor->dig($period, array_merge(\Ease\Functions::loadClassesInNamespace('AbraFlexi\Digest\Modules'), \Ease\Functions::loadClassesInNamespace('AbraFlexi\Digest\Modules\Daily')));
